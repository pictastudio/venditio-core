<?php

namespace PictaStudio\Venditio\Contracts;

interface OrderInvoiceTemplateInterface
{
    /**
     * @param  array<string, mixed>  $invoice
     */
    public function render(array $invoice): string;
}
