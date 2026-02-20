<?php

namespace PictaStudio\Venditio\Invoices;

class GeneratedOrderInvoice
{
    public function __construct(
        private readonly string $contents,
        private readonly string $fileName,
    ) {}

    public function contents(): string
    {
        return $this->contents;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }
}
