<?php

namespace PictaStudio\Venditio\Invoices;

use PictaStudio\Venditio\Contracts\OrderInvoiceTemplateInterface;

class DefaultOrderInvoiceTemplate implements OrderInvoiceTemplateInterface
{
    public function render(array $invoice): string
    {
        $sellerLines = (array) data_get($invoice, 'seller.address_lines', []);
        $sellerPhone = data_get($invoice, 'seller.phone');
        $sellerEmail = data_get($invoice, 'seller.email');
        $billingLines = (array) data_get($invoice, 'billing.lines', []);
        $paymentHistory = (array) data_get($invoice, 'payment_history', []);
        $footerLines = (array) data_get($invoice, 'footer.legal_lines', []);
        $brandMark = (string) config('venditio.order.invoice.brand_mark', '');
        $logo = (string) config('venditio.order.invoice.logo', '');

        if (is_string($sellerPhone) && $sellerPhone !== '') {
            $sellerLines[] = $sellerPhone;
        }

        if (is_string($sellerEmail) && $sellerEmail !== '') {
            $sellerLines[] = $sellerEmail;
        }

        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<style>
@page {
    margin: 18px 18px 14px;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "DejaVu Sans", sans-serif;
    background: #ffffff;
    color: #2a2a2a;
    font-size: 11px;
}
.page {
    width: 100%;
}
.top {
    display: table;
    width: 100%;
}
.top-left, .top-right {
    display: table-cell;
    vertical-align: top;
}
.top-right {
    text-align: right;
}
.title {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 10px;
}
.meta-table {
    border-collapse: collapse;
    font-size: 11px;
}
.meta-table td {
    padding: 2px 8px 2px 0;
    vertical-align: top;
}
.meta-label {
    font-weight: 700;
    white-space: nowrap;
}
.logo-mark {
    font-size: 20px;
    color: #6f6f6f;
    font-weight: 700;
    line-height: 1;
}
.logo-image {
    max-width: 42px;
    max-height: 42px;
}
.addresses {
    display: table;
    width: 100%;
    margin-top: 16px;
}
.address-col {
    display: table-cell;
    width: 50%;
    vertical-align: top;
    padding-right: 10px;
}
.address-title {
    margin: 0 0 6px;
    font-size: 11px;
    font-weight: 700;
}
.address-line {
    margin: 0;
    line-height: 1.4;
}
.summary-title {
    margin: 16px 0 12px;
    font-size: 17px;
    line-height: 1.3;
    font-weight: 700;
}
.table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: 11px;
}
.table thead th {
    font-size: 9px;
    color: #666;
    text-align: left;
    font-weight: 400;
    border-bottom: 1px solid #b8b8b8;
    padding: 0 6px 7px 0;
    line-height: 1.2;
}
.table thead th.num,
.table tbody td.num {
    text-align: right;
}
.table tbody td {
    padding: 8px 6px 0 0;
    vertical-align: top;
    line-height: 1.25;
    word-break: break-word;
}
.table thead th:last-child,
.table tbody td:last-child {
    padding-right: 0;
}
.invoice-lines col.desc {
    width: 44%;
}
.invoice-lines col.qty {
    width: 8%;
}
.invoice-lines col.unit {
    width: 18%;
}
.invoice-lines col.tax {
    width: 10%;
}
.invoice-lines col.amount {
    width: 20%;
}
.history-table col.method {
    width: 30%;
}
.history-table col.date {
    width: 22%;
}
.history-table col.paid {
    width: 24%;
}
.history-table col.receipt {
    width: 24%;
}
.history-table th:last-child,
.history-table td:last-child {
    word-break: break-all;
}
.item-period {
    margin-top: 2px;
    color: #525252;
}
.totals {
    margin-top: 10px;
    width: 58%;
    margin-left: auto;
    border-collapse: collapse;
}
.totals td {
    border-top: 1px solid #d5d5d5;
    padding: 3px 0;
    font-size: 11px;
    line-height: 1.3;
}
.totals .label {
    padding-right: 8px;
    word-break: break-word;
}
.totals .amount {
    text-align: right;
    white-space: nowrap;
}
.totals .highlight td {
    font-weight: 700;
}
.history-title {
    margin: 22px 0 10px;
    font-size: 17px;
    line-height: 1.25;
    font-weight: 700;
}
.footer {
    margin-top: 14px;
    font-size: 10px;
    color: #4e4e4e;
}
.bottom-line {
    border-top: 1px solid #d5d5d5;
    margin-top: 8px;
    padding-top: 6px;
    text-align: right;
    font-size: 9px;
    color: #5d5d5d;
    font-weight: 700;
}
</style>
</head>
<body>
    <div class="page">
        <div class="top">
            <div class="top-left">
                <p class="title">' . $this->escape((string) data_get($invoice, 'document_title', 'Ricevuta')) . '</p>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">' . $this->escape((string) data_get($invoice, 'invoice_number_label', 'Numero fattura')) . '</td>
                        <td>' . $this->escape((string) data_get($invoice, 'invoice_number', '')) . '</td>
                    </tr>
                    <tr>
                        <td class="meta-label">' . $this->escape((string) data_get($invoice, 'receipt_number_label', 'Numero ricevuta')) . '</td>
                        <td>' . $this->escape((string) data_get($invoice, 'receipt_number', '')) . '</td>
                    </tr>
                    <tr>
                        <td class="meta-label">' . $this->escape((string) data_get($invoice, 'payment_date_label', 'Data di pagamento')) . '</td>
                        <td>' . $this->escape((string) data_get($invoice, 'payment_date', '')) . '</td>
                    </tr>
                </table>
            </div>
            <div class="top-right">
                ' . $this->renderBrand($logo, $brandMark) . '
            </div>
        </div>

        <div class="addresses">
            <div class="address-col">
                ' . $this->renderLines([
            (string) data_get($invoice, 'seller.name', ''),
            ...$sellerLines,
        ], true) . '
            </div>
            <div class="address-col">
                <p class="address-title">' . $this->escape((string) data_get($invoice, 'billing.title', 'Indirizzo di fatturazione')) . '</p>
                ' . $this->renderLines($billingLines, false) . '
            </div>
        </div>

        <p class="summary-title">' . $this->escape((string) data_get($invoice, 'payment_summary', '')) . '</p>

        <table class="table invoice-lines">
            <colgroup>
                <col class="desc" />
                <col class="qty" />
                <col class="unit" />
                <col class="tax" />
                <col class="amount" />
            </colgroup>
            <thead>
                <tr>
                    <th>Descrizione</th>
                    <th class="num">Q.ta</th>
                    <th class="num">Prezzo unitario</th>
                    <th class="num">Imposte</th>
                    <th class="num">Importo</th>
                </tr>
            </thead>
            <tbody>
                ' . $this->renderItems((array) data_get($invoice, 'items', [])) . '
            </tbody>
        </table>

        <table class="totals">
            ' . $this->renderTotals((array) data_get($invoice, 'totals', [])) . '
        </table>

        <p class="history-title">Cronologia dei pagamenti</p>

        <table class="table history-table">
            <colgroup>
                <col class="method" />
                <col class="date" />
                <col class="paid" />
                <col class="receipt" />
            </colgroup>
            <thead>
                <tr>
                    <th>Metodo di pagamento</th>
                    <th>Data</th>
                    <th>Importo pagato</th>
                    <th class="num">Numero ricevuta</th>
                </tr>
            </thead>
            <tbody>
                ' . $this->renderPaymentHistory($paymentHistory) . '
            </tbody>
        </table>

        <div class="footer">
            ' . $this->renderLines($footerLines, false) . '
        </div>

        <div class="bottom-line">' . $this->escape((string) data_get($invoice, 'footer.page_number', 'Pagina 1 di 1')) . '</div>
    </div>
</body>
</html>';
    }

    /**
     * @param  array<int, mixed>  $items
     */
    private function renderItems(array $items): string
    {
        $rows = [];

        foreach ($items as $item) {
            $description = $this->escape((string) data_get($item, 'description', ''));
            $period = $this->escape((string) data_get($item, 'period', ''));
            $qty = $this->escape((string) data_get($item, 'qty', ''));
            $unitPrice = $this->escape((string) data_get($item, 'unit_price', ''));
            $tax = $this->escape((string) data_get($item, 'tax', ''));
            $amount = $this->escape((string) data_get($item, 'amount', ''));

            $rows[] = '<tr>
                <td>
                    <div>' . $description . '</div>
                    <div class="item-period">' . $period . '</div>
                </td>
                <td class="num">' . $qty . '</td>
                <td class="num">' . $unitPrice . '</td>
                <td class="num">' . $tax . '</td>
                <td class="num">' . $amount . '</td>
            </tr>';
        }

        return implode('', $rows);
    }

    /**
     * @param  array<int, mixed>  $totals
     */
    private function renderTotals(array $totals): string
    {
        $rows = [];

        foreach ($totals as $row) {
            $highlightClass = data_get($row, 'highlight') ? ' class="highlight"' : '';

            $rows[] = '<tr' . $highlightClass . '>
                <td class="label">' . $this->escape((string) data_get($row, 'label', '')) . '</td>
                <td class="amount">' . $this->escape((string) data_get($row, 'amount', '')) . '</td>
            </tr>';
        }

        return implode('', $rows);
    }

    /**
     * @param  array<int, mixed>  $rows
     */
    private function renderPaymentHistory(array $rows): string
    {
        $historyRows = [];

        foreach ($rows as $row) {
            $historyRows[] = '<tr>
                <td>' . $this->escape((string) data_get($row, 'payment_method', '')) . '</td>
                <td>' . $this->escape((string) data_get($row, 'date', '')) . '</td>
                <td>' . $this->escape((string) data_get($row, 'amount_paid', '')) . '</td>
                <td class="num">' . $this->escape((string) data_get($row, 'receipt_number', '')) . '</td>
            </tr>';
        }

        return implode('', $historyRows);
    }

    /**
     * @param  array<int, mixed>  $lines
     */
    private function renderLines(array $lines, bool $boldFirst): string
    {
        $html = '';
        $lines = array_values(array_filter(
            array_map(static fn (mixed $line): string => mb_trim((string) $line), $lines),
            static fn (string $line): bool => $line !== ''
        ));

        foreach ($lines as $index => $line) {
            $weight = $boldFirst && $index === 0 ? ' style="font-weight:700;"' : '';
            $html .= '<p class="address-line"' . $weight . '>' . $this->escape($line) . '</p>';
        }

        return $html;
    }

    private function renderBrand(string $logo, string $brandMark): string
    {
        if ($logo !== '') {
            return '<img class="logo-image" src="' . $this->escape($logo) . '" alt="logo" />';
        }

        if ($brandMark !== '') {
            return '<div class="logo-mark">' . $this->escape($brandMark) . '</div>';
        }

        return '';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
