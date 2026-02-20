<?php

namespace PictaStudio\Venditio\Invoices;

use Carbon\CarbonInterface;
use Illuminate\Support\{Carbon, Fluent};
use PictaStudio\Venditio\Contracts\OrderInvoiceDataFactoryInterface;
use PictaStudio\Venditio\Models\{Order, OrderLine};

class DefaultOrderInvoiceDataFactory implements OrderInvoiceDataFactoryInterface
{
    public function make(Order $order): array
    {
        $order->loadMissing('lines');

        $addresses = $this->normalizeAddresses($order->addresses);
        $billingAddress = (array) data_get($addresses, 'billing', []);
        $paymentDate = $order->approved_at ?? $order->created_at ?? now();
        $receiptNumber = (string) ($order->identifier ?: $order->getKey());
        $invoicePrefix = (string) config('venditio.order.invoice.number_prefix', 'INV');
        $invoiceNumber = sprintf(
            '%s-%s',
            $invoicePrefix,
            mb_strtoupper(mb_substr(hash('sha1', $receiptNumber), 0, 8))
        );

        $netTotal = max(
            0,
            (float) $order->sub_total_taxable
            + (float) $order->shipping_fee
            + (float) $order->payment_fee
            - (float) $order->discount_amount
        );

        $taxTotal = max(0, (float) $order->sub_total_tax);
        $total = max(0, (float) $order->total_final);

        return [
            'document_title' => (string) config('venditio.order.invoice.labels.document_title', 'Ricevuta'),
            'invoice_number_label' => (string) config('venditio.order.invoice.labels.invoice_number', 'Numero fattura'),
            'receipt_number_label' => (string) config('venditio.order.invoice.labels.receipt_number', 'Numero ricevuta'),
            'payment_date_label' => (string) config('venditio.order.invoice.labels.payment_date', 'Data di pagamento'),
            'invoice_number' => $invoiceNumber,
            'receipt_number' => $receiptNumber,
            'payment_date' => $this->formatDate($paymentDate),
            'seller' => $this->sellerData(),
            'billing' => [
                'title' => (string) config('venditio.order.invoice.labels.billing_address', 'Indirizzo di fatturazione'),
                'lines' => $this->billingAddressLines($order, $billingAddress),
            ],
            'payment_summary' => sprintf(
                '%s %s %s %s',
                (string) config('venditio.order.invoice.labels.payment_of', 'Pagamento di'),
                $this->formatMoney($total),
                (string) config('venditio.order.invoice.labels.paid_on', 'effettuato in data'),
                $this->formatDate($paymentDate)
            ),
            'items' => $this->lineItems($order),
            'totals' => $this->totals(
                netTotal: $netTotal,
                taxTotal: $taxTotal,
                total: $total,
                taxRate: $this->extractTaxRate($order),
                taxCountry: (string) data_get($billingAddress, 'country_name', data_get($billingAddress, 'country', ''))
            ),
            'payment_history' => [
                [
                    'payment_method' => (string) data_get($order, 'payment_method', config('venditio.order.invoice.default_payment_method', 'N/A')),
                    'date' => $this->formatDate($paymentDate),
                    'amount_paid' => $this->formatMoney($total),
                    'receipt_number' => $receiptNumber,
                ],
            ],
            'footer' => [
                'legal_lines' => $this->sellerFooterLines(),
                'page_number' => (string) config('venditio.order.invoice.labels.page_number', 'Pagina 1 di 1'),
            ],
        ];
    }

    /**
     * @return array<int, array{description: string, period: string, qty: string, unit_price: string, tax: string, amount: string}>
     */
    private function lineItems(Order $order): array
    {
        return $order->lines
            ->map(function (OrderLine $line): array {
                $lineAmount = max(0, (float) $line->unit_final_price_taxable * (int) $line->qty);
                $period = $this->extractLinePeriod($line);

                return [
                    'description' => (string) $line->product_name,
                    'period' => $period,
                    'qty' => (string) ((int) $line->qty),
                    'unit_price' => $this->formatMoney((float) $line->unit_price),
                    'tax' => mb_rtrim(mb_rtrim((string) number_format((float) $line->tax_rate, 2, '.', ''), '0'), '.') . '%',
                    'amount' => $this->formatMoney($lineAmount),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, amount: string, highlight: bool}>
     */
    private function totals(float $netTotal, float $taxTotal, float $total, float $taxRate, string $taxCountry): array
    {
        $taxCountry = filled($taxCountry) ? $taxCountry : (string) config('venditio.order.invoice.default_tax_country', 'Tax');

        return [
            [
                'label' => (string) config('venditio.order.invoice.labels.subtotal', 'Subtotale'),
                'amount' => $this->formatMoney($netTotal),
                'highlight' => false,
            ],
            [
                'label' => (string) config('venditio.order.invoice.labels.net_total', 'Totale al netto delle imposte'),
                'amount' => $this->formatMoney($netTotal),
                'highlight' => false,
            ],
            [
                'label' => sprintf(
                    '%s - %s (%s%% %s %s)',
                    (string) config('venditio.order.invoice.labels.tax', 'IVA'),
                    $taxCountry,
                    mb_rtrim(mb_rtrim((string) number_format($taxRate, 2, '.', ''), '0'), '.'),
                    (string) config('venditio.order.invoice.labels.on', 'su'),
                    $this->formatMoney($netTotal)
                ),
                'amount' => $this->formatMoney($taxTotal),
                'highlight' => false,
            ],
            [
                'label' => (string) config('venditio.order.invoice.labels.total', 'Totale'),
                'amount' => $this->formatMoney($total),
                'highlight' => true,
            ],
            [
                'label' => (string) config('venditio.order.invoice.labels.amount_paid', 'Importo pagato'),
                'amount' => $this->formatMoney($total),
                'highlight' => true,
            ],
        ];
    }

    /**
     * @return array{name: string, address_lines: array<int, string>, phone: ?string, email: ?string}
     */
    private function sellerData(): array
    {
        $addressLines = config('venditio.order.invoice.seller.address_lines', []);
        $addressLines = is_string($addressLines)
            ? preg_split('/\r\n|\r|\n/', $addressLines) ?: []
            : (is_array($addressLines) ? $addressLines : []);

        return [
            'name' => (string) config('venditio.order.invoice.seller.name', config('app.name', 'Venditio')),
            'address_lines' => array_values(
                array_filter(
                    array_map(
                        static fn (mixed $line): string => mb_trim((string) $line),
                        $addressLines
                    ),
                    static fn (string $line): bool => $line !== ''
                )
            ),
            'phone' => config('venditio.order.invoice.seller.phone'),
            'email' => config('venditio.order.invoice.seller.email'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function sellerFooterLines(): array
    {
        $footerLines = config('venditio.order.invoice.seller.footer_lines', []);
        $footerLines = is_string($footerLines)
            ? preg_split('/\r\n|\r|\n/', $footerLines) ?: []
            : (is_array($footerLines) ? $footerLines : []);

        return array_values(
            array_filter(
                array_map(
                    static fn (mixed $line): string => mb_trim((string) $line),
                    $footerLines
                ),
                static fn (string $line): bool => $line !== ''
            )
        );
    }

    /**
     * @param  array<string, mixed>  $billingAddress
     * @return array<int, string>
     */
    private function billingAddressLines(Order $order, array $billingAddress): array
    {
        return array_values(
            array_filter([
                (string) (data_get($billingAddress, 'full_name')
                    ?: mb_trim(implode(' ', array_filter([$order->user_first_name, $order->user_last_name])))),
                (string) data_get($billingAddress, 'street', data_get($billingAddress, 'address_line_1', '')),
                mb_trim(
                    implode(' ', array_filter([
                        data_get($billingAddress, 'postal_code'),
                        data_get($billingAddress, 'city', data_get($billingAddress, 'municipality_name')),
                        data_get($billingAddress, 'province_name', data_get($billingAddress, 'province')),
                    ]))
                ),
                (string) data_get($billingAddress, 'country_name', data_get($billingAddress, 'country', '')),
                (string) ($order->user_email ?: data_get($billingAddress, 'email', '')),
            ])
        );
    }

    private function formatMoney(float $amount): string
    {
        $locale = (string) config('venditio.order.invoice.locale', config('app.locale', 'en'));
        $isItalianLike = str_starts_with(mb_strtolower($locale), 'it');
        $decimalSeparator = (string) config('venditio.order.invoice.currency.decimal_separator', $isItalianLike ? ',' : '.');
        $thousandsSeparator = (string) config('venditio.order.invoice.currency.thousands_separator', $isItalianLike ? '.' : ',');
        $currencyCode = (string) config('venditio.order.invoice.currency.code', 'EUR');
        $decimals = (int) config('venditio.order.invoice.currency.decimals', 2);

        return number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator) . ' ' . $currencyCode;
    }

    private function formatDate(CarbonInterface $date): string
    {
        $locale = (string) config('venditio.order.invoice.locale', config('app.locale', 'en'));

        return $date->copy()->locale($locale)->translatedFormat('j F Y');
    }

    private function normalizeAddresses(mixed $addresses): array
    {
        return match (true) {
            is_array($addresses) => $addresses,
            $addresses instanceof Fluent => $addresses->toArray(),
            is_string($addresses) => (array) json_decode($addresses, true),
            default => [],
        };
    }

    private function extractTaxRate(Order $order): float
    {
        $firstLine = $order->lines->first();

        if (!$firstLine instanceof OrderLine) {
            return 0;
        }

        return (float) $firstLine->tax_rate;
    }

    private function extractLinePeriod(OrderLine $line): string
    {
        $productData = $line->product_data;

        if (is_array($productData)) {
            $period = data_get($productData, 'period');

            if (is_string($period) && $period !== '') {
                return $period;
            }
        }

        $start = $line->created_at instanceof Carbon
            ? $line->created_at
            : now();

        return sprintf(
            '%s-%s',
            $start->copy()->format('d M Y'),
            $start->copy()->addMonth()->format('d M Y'),
        );
    }
}
