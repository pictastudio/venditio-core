<?php

namespace PictaStudio\Venditio\Contracts;

use PictaStudio\Venditio\Models\Order;

interface OrderInvoiceDataFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function make(Order $order): array;
}
