<?php

namespace PictaStudio\Venditio\Contracts;

interface OrderInvoiceRendererInterface
{
    /**
     * @param  array<string, mixed>  $invoice
     */
    public function render(string $document, array $invoice): string;
}
