<?php

namespace PictaStudio\Venditio\Invoices;

use Dompdf\{Dompdf, Options};
use PictaStudio\Venditio\Contracts\OrderInvoiceRendererInterface;

class DompdfOrderInvoiceRenderer implements OrderInvoiceRendererInterface
{
    public function render(string $document, array $invoice): string
    {
        $options = new Options;
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsRemoteEnabled((bool) config('venditio.order.invoice.pdf.enable_remote_assets', false));
        $options->setDefaultFont($this->resolveDefaultFont());
        $options->setIsPhpEnabled(false);
        $options->setIsFontSubsettingEnabled((bool) config('venditio.order.invoice.pdf.font_subsetting', false));
        $options->setChroot(base_path());

        $tempDir = $this->resolveWritableDirectory(
            config('venditio.order.invoice.pdf.temp_dir'),
            function_exists('storage_path')
                ? storage_path('app/venditio/dompdf/tmp')
                : null
        );

        if ($tempDir !== null) {
            $options->setTempDir($tempDir);
        }

        $fontCacheDir = $this->resolveWritableDirectory(
            config('venditio.order.invoice.pdf.font_cache_dir'),
            $tempDir !== null
                ? $tempDir . DIRECTORY_SEPARATOR . 'fonts'
                : (function_exists('storage_path')
                    ? storage_path('app/venditio/dompdf/fonts')
                    : null)
        );

        if ($fontCacheDir !== null) {
            $options->setFontCache($fontCacheDir);
        }

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($document, 'UTF-8');
        $dompdf->setPaper(
            size: (string) config('venditio.order.invoice.pdf.paper', 'letter'),
            orientation: (string) config('venditio.order.invoice.pdf.orientation', 'portrait')
        );
        $dompdf->render();

        return $dompdf->output();
    }

    private function resolveDefaultFont(): string
    {
        $font = mb_trim((string) config('venditio.order.invoice.pdf.default_font', 'dejavu sans'));

        return $font !== '' ? $font : 'dejavu sans';
    }

    private function resolveWritableDirectory(mixed $preferred, mixed $fallback): ?string
    {
        $candidates = [
            $preferred,
            $fallback,
            sys_get_temp_dir(),
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }

            $directory = mb_rtrim(mb_trim($candidate), DIRECTORY_SEPARATOR);
            if ($directory === '') {
                continue;
            }

            if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
                continue;
            }

            if (is_writable($directory)) {
                return $directory;
            }
        }

        return null;
    }
}
