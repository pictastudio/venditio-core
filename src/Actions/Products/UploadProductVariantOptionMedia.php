<?php

namespace PictaStudio\Venditio\Actions\Products;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use PictaStudio\Venditio\Models\{Product, ProductVariantOption};
use PictaStudio\Venditio\Support\{CatalogImage, ProductMedia};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UploadProductVariantOptionMedia
{
    public function handle(Product $product, ProductVariantOption $productVariantOption, array $payload): Collection
    {
        $matchingProducts = $this->resolveMatchingProducts($product, $productVariantOption);

        if ($matchingProducts->isEmpty()) {
            throw ValidationException::withMessages([
                'product_variant_option_id' => ['The selected variant option does not match the target product.'],
            ]);
        }

        $storedMedia = [
            'images' => $this->storeSharedImageCollection(
                Arr::get($payload, 'images'),
                $product,
                $productVariantOption
            ),
            'files' => $this->storeSharedFileCollection(
                Arr::get($payload, 'files'),
                $product,
                $productVariantOption
            ),
        ];

        if ($storedMedia['images'] === [] && $storedMedia['files'] === []) {
            throw ValidationException::withMessages([
                'images' => ['At least one image or file upload is required.'],
                'files' => ['At least one image or file upload is required.'],
            ]);
        }

        $matchingProducts->each(function (Product $matchingProduct) use ($storedMedia): void {
            $images = CatalogImage::normalizeCollection($matchingProduct->getAttribute('images'));
            $files = ProductMedia::normalizeCollection($matchingProduct->getAttribute('files'), isImage: false);
            $usedIds = array_values(array_unique([
                ...CatalogImage::collectUsedIds($images),
                ...ProductMedia::collectUsedIds($files),
            ]));

            $matchingProduct->forceFill([
                'images' => [
                    ...$images,
                    ...$this->cloneImageItems($storedMedia['images'], $images, $usedIds),
                ],
                'files' => [
                    ...$files,
                    ...$this->cloneFileItems($storedMedia['files'], $files, $usedIds),
                ],
            ]);
            $matchingProduct->save();
        });

        return $matchingProducts
            ->map(fn (Product $matchingProduct) => $matchingProduct->refresh()->load(['inventory', 'variantOptions.productVariant']));
    }

    private function resolveMatchingProducts(Product $product, ProductVariantOption $productVariantOption): Collection
    {
        $productModelClass = resolve_model('product');

        $query = $productModelClass::withoutGlobalScopes()
            ->whereHas('variantOptions', fn ($builder) => $builder->whereKey($productVariantOption->getKey()));

        if ($product->parent_id) {
            return $query
                ->whereKey($product->getKey())
                ->get();
        }

        return $query
            ->where('parent_id', $product->getKey())
            ->get();
    }

    private function storeSharedImageCollection(
        mixed $items,
        Product $product,
        ProductVariantOption $productVariantOption
    ): array {
        $items = is_array($items) ? $items : [];

        return collect($items)
            ->map(function (array $item, int $index) use ($product, $productVariantOption): array {
                /** @var UploadedFile $file */
                $file = $item['file'];

                return [
                    'src' => $file->store(
                        "products/{$product->getKey()}/variant_options/{$productVariantOption->getKey()}/images",
                        'public'
                    ),
                    'type' => null,
                    'alt' => Arr::get($item, 'alt'),
                    'name' => Arr::get($item, 'name'),
                    'mimetype' => Arr::get($item, 'mimetype', $file->getMimeType()),
                    'sort_order' => CatalogImage::resolveSortOrder(Arr::get($item, 'sort_order'), $index),
                ];
            })
            ->values()
            ->all();
    }

    private function storeSharedFileCollection(
        mixed $items,
        Product $product,
        ProductVariantOption $productVariantOption
    ): array {
        $items = is_array($items) ? $items : [];

        return collect($items)
            ->map(function (array $item, int $index) use ($product, $productVariantOption): array {
                /** @var UploadedFile $file */
                $file = $item['file'];

                return [
                    'src' => $file->store(
                        "products/{$product->getKey()}/variant_options/{$productVariantOption->getKey()}/files",
                        'public'
                    ),
                    'alt' => Arr::get($item, 'alt'),
                    'name' => Arr::get($item, 'name'),
                    'mimetype' => Arr::get($item, 'mimetype', $file->getMimeType()),
                    'sort_order' => ProductMedia::resolveSortOrder(Arr::get($item, 'sort_order'), $index),
                    'active' => ProductMedia::resolveBoolean(Arr::get($item, 'active'), true),
                    'shared_from_variant_option' => true,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $currentItems
     * @param  array<int, string>  $usedIds
     * @return array<int, array<string, mixed>>
     */
    private function cloneImageItems(array $items, array $currentItems, array &$usedIds): array
    {
        if ($items === []) {
            return [];
        }

        $sharedSortOffset = collect($currentItems)
            ->filter(fn (array $item): bool => $this->isSharedVariantOptionImage(Arr::get($item, 'src')))
            ->max('sort_order');

        $nextSortOrder = is_numeric($sharedSortOffset) ? ((int) $sharedSortOffset + 1) : 0;

        return collect($items)
            ->map(function (array $item) use (&$usedIds, &$nextSortOrder): array {
                $sortOrder = CatalogImage::resolveSortOrder(Arr::get($item, 'sort_order'), $nextSortOrder);
                $nextSortOrder++;

                return [
                    'id' => CatalogImage::generateUniqueId($usedIds),
                    'type' => null,
                    'src' => Arr::get($item, 'src'),
                    'alt' => Arr::get($item, 'alt'),
                    'name' => Arr::get($item, 'name'),
                    'mimetype' => Arr::get($item, 'mimetype'),
                    'sort_order' => $sortOrder,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $currentItems
     * @param  array<int, string>  $usedIds
     * @return array<int, array<string, mixed>>
     */
    private function cloneFileItems(array $items, array $currentItems, array &$usedIds): array
    {
        if ($items === []) {
            return [];
        }

        $sharedSortOffset = collect($currentItems)
            ->filter(fn (array $item): bool => (bool) Arr::get($item, 'shared_from_variant_option', false))
            ->max('sort_order');

        $nextSortOrder = is_numeric($sharedSortOffset) ? ((int) $sharedSortOffset + 1) : 0;

        return collect($items)
            ->map(function (array $item) use (&$usedIds, &$nextSortOrder): array {
                $sortOrder = ProductMedia::resolveSortOrder(Arr::get($item, 'sort_order'), $nextSortOrder);
                $nextSortOrder++;

                return [
                    'id' => ProductMedia::generateUniqueId($usedIds),
                    'src' => Arr::get($item, 'src'),
                    'alt' => Arr::get($item, 'alt'),
                    'name' => Arr::get($item, 'name'),
                    'mimetype' => Arr::get($item, 'mimetype'),
                    'sort_order' => $sortOrder,
                    'active' => ProductMedia::resolveBoolean(Arr::get($item, 'active'), true),
                    'shared_from_variant_option' => true,
                ];
            })
            ->values()
            ->all();
    }

    private function isSharedVariantOptionImage(mixed $src): bool
    {
        return is_string($src)
            && str_contains($src, '/variant_options/')
            && str_contains($src, '/images/');
    }
}
