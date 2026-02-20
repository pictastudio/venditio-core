<?php

namespace PictaStudio\Venditio\Actions\Orders;

use Illuminate\Support\Str;
use PictaStudio\Venditio\Contracts\{OrderInvoiceDataFactoryInterface, OrderInvoiceRendererInterface, OrderInvoiceTemplateInterface};
use PictaStudio\Venditio\Invoices\GeneratedOrderInvoice;
use PictaStudio\Venditio\Models\Order;

class GenerateOrderInvoice
{
    public function __construct(
        private readonly OrderInvoiceDataFactoryInterface $dataFactory,
        private readonly OrderInvoiceTemplateInterface $template,
        private readonly OrderInvoiceRendererInterface $renderer,
    ) {}

    public function execute(Order $order): GeneratedOrderInvoice
    {
        $invoice = $this->dataFactory->make($order);
        $document = $this->template->render($invoice);

        return new GeneratedOrderInvoice(
            contents: $this->renderer->render($document, $invoice),
            fileName: $this->resolveFileName($order, $invoice),
        );
    }

    /**
     * @param  array<string, mixed>  $invoice
     */
    private function resolveFileName(Order $order, array $invoice): string
    {
        $base = (string) config('venditio.order.invoice.filename_prefix', 'invoice');
        $identifier = (string) data_get($invoice, 'receipt_number', $order->identifier ?: $order->getKey());
        $identifier = Str::of($identifier)->slug('_')->value();
        $identifier = $identifier !== '' ? $identifier : (string) $order->getKey();

        return "{$base}-{$identifier}.pdf";
    }
}
