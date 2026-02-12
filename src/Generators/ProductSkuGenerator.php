<?php

namespace PictaStudio\Venditio\Generators;

use Illuminate\Support\Str;
use PictaStudio\Venditio\Contracts\ProductSkuGeneratorInterface;
use PictaStudio\Venditio\Models\Product;

use function PictaStudio\Venditio\Helpers\Functions\query;

class ProductSkuGenerator implements ProductSkuGeneratorInterface
{
    public function forProductPayload(array $payload): string
    {
        $name = (string) ($payload['name'] ?? 'product');
        $seed = Str::upper(Str::slug($name, '-'));

        if (blank($seed)) {
            $seed = 'PRODUCT';
        }

        return $this->uniqueSku($seed);
    }

    public function forVariant(Product $baseProduct, array $options): string
    {
        $baseSku = filled($baseProduct->sku)
            ? (string) $baseProduct->sku
            : 'P' . $baseProduct->getKey();

        $signature = collect($options)
            ->filter(fn (mixed $option): bool => is_object($option))
            ->map(fn (object $option): int => (int) ($option->id ?? 0))
            ->filter()
            ->sort()
            ->implode('-');

        if (blank($signature)) {
            $signature = 'VARIANT';
        }

        return $this->uniqueSku($baseSku . '-' . $signature);
    }

    private function uniqueSku(string $seed): string
    {
        $normalizedSeed = mb_trim($seed);

        if ($normalizedSeed === '') {
            $normalizedSeed = 'SKU';
        }

        $candidate = $this->truncate($normalizedSeed);

        if (!$this->skuExists($candidate)) {
            return $candidate;
        }

        $counter = 1;

        do {
            $suffix = '-' . $counter;
            $candidate = $this->truncate($normalizedSeed, $suffix);
            $counter++;
        } while ($this->skuExists($candidate));

        return $candidate;
    }

    private function skuExists(string $sku): bool
    {
        return query('product')
            ->withoutGlobalScopes()
            ->where('sku', $sku)
            ->exists();
    }

    private function truncate(string $seed, string $suffix = ''): string
    {
        $maxLength = 255 - mb_strlen($suffix);

        return mb_substr($seed, 0, max(1, $maxLength)) . $suffix;
    }
}
