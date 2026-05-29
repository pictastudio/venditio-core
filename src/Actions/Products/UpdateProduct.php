<?php

namespace PictaStudio\Venditio\Actions\Products;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PictaStudio\Venditio\Models\Product;
use PictaStudio\Venditio\Support\{CatalogImage, ProductMedia};

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class UpdateProduct
{
    public function handle(Product $product, array $payload): Product
    {
        $categoryIdsProvided = array_key_exists('category_ids', $payload);
        $categoryIds = Arr::pull($payload, 'category_ids', []);
        $collectionIdsProvided = array_key_exists('collection_ids', $payload);
        $collectionIds = Arr::pull($payload, 'collection_ids', []);
        $tagIdsProvided = array_key_exists('tag_ids', $payload);
        $tagIds = Arr::pull($payload, 'tag_ids', []);
        $relatedProductIdsProvided = array_key_exists('related_product_ids', $payload);
        $relatedProductIds = Arr::pull($payload, 'related_product_ids', []);
        $inventoryProvided = array_key_exists('inventory', $payload);
        $inventoryPayload = Arr::pull($payload, 'inventory');
        $imagesProvided = array_key_exists('images', $payload);
        $filesProvided = array_key_exists('files', $payload);
        $sharedMediaUpdates = [];

        if ($tagIdsProvided) {
            $this->validateTagProductTypeCompatibility(
                $tagIds ?? [],
                $payload['product_type_id'] ?? $product->product_type_id
            );
        }

        $product->fill(
            $this->prepareMediaPayload($product, $payload, $imagesProvided, $filesProvided, $sharedMediaUpdates)
        );
        $product->save();

        $this->propagateSharedVariantOptionMediaUpdates($product, $sharedMediaUpdates);

        if ($categoryIdsProvided) {
            $product->categories()->sync($categoryIds ?? []);
        }

        if ($collectionIdsProvided) {
            $this->syncCollections($product, $collectionIds ?? []);
        }

        if ($tagIdsProvided) {
            $product->tags()->sync($tagIds ?? []);
        }

        if ($relatedProductIdsProvided) {
            $this->syncRelatedProducts($product, $relatedProductIds ?? []);
        }

        if ($inventoryProvided && is_array($inventoryPayload)) {
            $product->inventory()->updateOrCreate(
                ['product_id' => $product->getKey()],
                $inventoryPayload
            );
        }

        return $product->refresh()->load(['inventory', 'variantOptions']);
    }

    private function syncRelatedProducts(Product $product, array $relatedProductIds): void
    {
        $productKey = (int) $product->getKey();
        $syncPayload = collect($relatedProductIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->reject(fn (int $id): bool => $id === $productKey)
            ->unique()
            ->values()
            ->mapWithKeys(fn (int $id, int $index): array => [
                $id => ['sort_order' => $index],
            ])
            ->all();

        $product->relatedProducts()->sync($syncPayload);
    }

    private function syncCollections(Product $product, array $collectionIds): void
    {
        $collectionIds = collect($collectionIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($collectionIds->isEmpty()) {
            $product->collections()->sync([]);

            return;
        }

        $relation = $product->collections();
        $pivotTable = $relation->getTable();
        $productPivotKey = $relation->getForeignPivotKeyName();
        $collectionPivotKey = $relation->getRelatedPivotKeyName();

        $existingSortOrders = DB::table($pivotTable)
            ->where($productPivotKey, $product->getKey())
            ->whereIn($collectionPivotKey, $collectionIds->all())
            ->pluck('sort_order', $collectionPivotKey);

        $newCollectionIds = $collectionIds
            ->reject(fn (int $collectionId): bool => $existingSortOrders->has($collectionId))
            ->values();

        $maxSortOrders = $newCollectionIds->isEmpty()
            ? collect()
            : DB::table($pivotTable)
                ->whereIn($collectionPivotKey, $newCollectionIds->all())
                ->select($collectionPivotKey, DB::raw('MAX(sort_order) as max_sort_order'))
                ->groupBy($collectionPivotKey)
                ->pluck('max_sort_order', $collectionPivotKey);

        $syncPayload = $collectionIds
            ->mapWithKeys(function (int $collectionId) use ($existingSortOrders, $maxSortOrders): array {
                if ($existingSortOrders->has($collectionId)) {
                    return [
                        $collectionId => ['sort_order' => (int) $existingSortOrders->get($collectionId)],
                    ];
                }

                $maxSortOrder = $maxSortOrders->get($collectionId);

                return [
                    $collectionId => ['sort_order' => $maxSortOrder === null ? 0 : (int) $maxSortOrder + 1],
                ];
            })
            ->all();

        $product->collections()->sync($syncPayload);
    }

    private function validateTagProductTypeCompatibility(array $tagIds, mixed $productTypeId): void
    {
        if ($tagIds === []) {
            return;
        }

        $resolvedProductTypeId = is_numeric($productTypeId) ? (int) $productTypeId : null;
        $invalidTags = resolve_model('tag')::withoutGlobalScopes()
            ->whereKey($tagIds)
            ->whereNotNull('product_type_id')
            ->when(
                $resolvedProductTypeId !== null,
                fn ($query) => $query->where('product_type_id', '!=', $resolvedProductTypeId),
                fn ($query) => $query
                    ->whereNotNull('product_type_id')
            )
            ->pluck('id')
            ->values()
            ->all();

        if ($invalidTags === []) {
            return;
        }

        throw ValidationException::withMessages([
            'tag_ids' => [
                'The selected tags are not compatible with the product type: ' . implode(', ', $invalidTags),
            ],
        ]);
    }

    private function prepareMediaPayload(
        Product $product,
        array $payload,
        bool $imagesProvided,
        bool $filesProvided,
        array &$sharedMediaUpdates
    ): array {
        $currentImages = CatalogImage::normalizeCollection($product->getAttribute('images'));
        $currentFiles = ProductMedia::normalizeCollection($product->getAttribute('files'), isImage: false);
        $validationErrors = [];

        if ($imagesProvided) {
            try {
                CatalogImage::validatePayload(
                    Arr::get($payload, 'images'),
                    CatalogImage::collectUsedIds($currentImages),
                    'images',
                    $currentImages
                );
            } catch (ValidationException $exception) {
                $validationErrors = array_replace_recursive($validationErrors, $exception->errors());
            }
        }

        if ($filesProvided) {
            $validationErrors = array_replace_recursive($validationErrors, $this->collectFileItemPayloadErrors(
                Arr::get($payload, 'files'),
                'files',
                ProductMedia::collectUsedIds($currentFiles)
            ));
        }

        if ($validationErrors !== []) {
            throw ValidationException::withMessages($validationErrors);
        }

        if ($imagesProvided) {
            $updatedImages = CatalogImage::mergeCollection(
                $product,
                $currentImages,
                Arr::get($payload, 'images'),
                'products'
            );

            $this->trackSharedVariantOptionImageUpdates($currentImages, $updatedImages ?? [], $sharedMediaUpdates);

            $payload['images'] = $updatedImages;
        }

        if ($filesProvided) {
            $usedFileIds = ProductMedia::collectUsedIds($currentFiles);
            $payload['files'] = $this->mergeFileCollection(
                $product,
                Arr::get($payload, 'files'),
                $currentFiles,
                $usedFileIds,
                $sharedMediaUpdates
            );
        }

        return $payload;
    }

    private function mergeFileCollection(
        Product $product,
        mixed $items,
        array $currentFiles,
        array &$usedFileIds,
        array &$sharedMediaUpdates
    ): ?array {
        if ($items === null) {
            return null;
        }

        $items = is_array($items) ? $items : [];
        $currentFilesById = collect($currentFiles)
            ->keyBy(fn (array $item) => (string) Arr::get($item, 'id'))
            ->all();

        foreach ($items as $item) {
            $mediaId = Arr::get($item, 'id');

            if (is_string($mediaId) && $mediaId !== '') {
                /** @var array<string, mixed> $existingItem */
                $existingItem = $currentFilesById[$mediaId];
                $currentFilesById[$mediaId] = ProductMedia::mergeItem($existingItem, $item, false);
                $this->trackSharedVariantOptionFileUpdate(
                    $existingItem,
                    $currentFilesById[$mediaId],
                    $sharedMediaUpdates
                );

                continue;
            }

            /** @var UploadedFile $file */
            $file = $item['file'];
            $generatedId = ProductMedia::generateUniqueId($usedFileIds);
            $currentFilesById[$generatedId] = [
                'id' => $generatedId,
                'src' => $file->store("products/{$product->getKey()}/files", 'public'),
                'alt' => Arr::get($item, 'alt'),
                'name' => Arr::get($item, 'name'),
                'mimetype' => Arr::get($item, 'mimetype', $file->getMimeType()),
                'sort_order' => ProductMedia::resolveSortOrder(
                    Arr::get($item, 'sort_order'),
                    count($currentFilesById)
                ),
                'active' => ProductMedia::resolveBoolean(Arr::get($item, 'active'), true),
                'shared_from_variant_option' => false,
            ];
        }

        return array_values($currentFilesById);
    }

    private function trackSharedVariantOptionImageUpdates(array $currentImages, array $updatedImages, array &$sharedMediaUpdates): void
    {
        $currentImagesById = collect($currentImages)
            ->keyBy(fn (array $image) => (string) Arr::get($image, 'id'))
            ->all();

        foreach ($updatedImages as $updatedImage) {
            $imageId = Arr::get($updatedImage, 'id');
            $existingImage = is_scalar($imageId) ? ($currentImagesById[(string) $imageId] ?? null) : null;

            if (!is_array($existingImage)) {
                continue;
            }

            $src = Arr::get($existingImage, 'src');

            if (!$this->isSharedVariantOptionImage($src) || Arr::get($updatedImage, 'src') !== $src) {
                continue;
            }

            $metadata = Arr::only($updatedImage, [
                'type',
                'name',
                'alt',
                'mimetype',
                'sort_order',
            ]);

            if ($metadata === Arr::only($existingImage, array_keys($metadata))) {
                continue;
            }

            $sharedMediaUpdates[] = [
                'collection' => 'images',
                'src' => $src,
                'metadata' => $metadata,
            ];
        }
    }

    private function trackSharedVariantOptionFileUpdate(
        array $existingItem,
        array $updatedItem,
        array &$sharedMediaUpdates
    ): void {
        if (!(bool) Arr::get($existingItem, 'shared_from_variant_option', false)) {
            return;
        }

        $src = Arr::get($existingItem, 'src');

        if (!is_string($src) || blank($src)) {
            return;
        }

        $metadata = Arr::only($updatedItem, [
            'name',
            'alt',
            'mimetype',
            'sort_order',
            'active',
        ]);

        $sharedMediaUpdates[] = [
            'collection' => 'files',
            'src' => $src,
            'metadata' => $metadata,
        ];
    }

    private function propagateSharedVariantOptionMediaUpdates(Product $updatedProduct, array $updates): void
    {
        if ($updates === []) {
            return;
        }

        $productModelClass = resolve_model('product');

        $productModelClass::withoutGlobalScopes()
            ->whereKeyNot($updatedProduct->getKey())
            ->get(['id', 'images', 'files'])
            ->each(function (Product $product) use ($updates): void {
                $images = CatalogImage::normalizeCollection($product->getAttribute('images'));
                $files = ProductMedia::normalizeCollection($product->getAttribute('files'), isImage: false);
                $changed = false;

                foreach ($updates as $update) {
                    $collection = (string) Arr::get($update, 'collection');
                    $src = Arr::get($update, 'src');
                    $metadata = Arr::get($update, 'metadata', []);

                    if (!is_string($src) || !is_array($metadata)) {
                        continue;
                    }

                    if ($collection === 'images') {
                        $images = collect($images)
                            ->map(function (array $item) use ($src, $metadata, &$changed): array {
                                if (
                                    Arr::get($item, 'src') !== $src
                                    || !$this->isSharedVariantOptionImage(Arr::get($item, 'src'))
                                ) {
                                    return $item;
                                }

                                $changed = true;

                                return CatalogImage::mergeItem($item, $metadata);
                            })
                            ->values()
                            ->all();

                        continue;
                    }

                    if ($collection !== 'files') {
                        continue;
                    }

                    $files = collect($files)
                        ->map(function (array $item) use ($src, $metadata, &$changed): array {
                            if (
                                Arr::get($item, 'src') !== $src
                                || !(bool) Arr::get($item, 'shared_from_variant_option', false)
                            ) {
                                return $item;
                            }

                            $changed = true;

                            return ProductMedia::mergeItem($item, $metadata, false);
                        })
                        ->values()
                        ->all();
                }

                if (!$changed) {
                    return;
                }

                $product->forceFill([
                    'images' => $images,
                    'files' => $files,
                ]);
                $product->save();
            });
    }

    /**
     * @param  array<int, string>  $existingIds
     * @return array<string, array<int, string>>
     */
    private function collectFileItemPayloadErrors(
        mixed $items,
        string $attribute,
        array $existingIds
    ): array {
        if ($items === null) {
            return [];
        }

        $items = is_array($items) ? $items : [];
        $errors = [];

        foreach ($items as $index => $item) {
            $errors += $this->validateMediaItemPayload(
                is_array($item) ? $item : [],
                $attribute,
                $index,
                $existingIds
            );
        }

        return $errors;
    }

    /**
     * @param  array<int, string>  $existingIds
     * @return array<string, array<int, string>>
     */
    private function validateMediaItemPayload(
        array $item,
        string $attribute,
        int $index,
        array $existingIds
    ): array {
        $hasId = is_string(Arr::get($item, 'id')) && Arr::get($item, 'id') !== '';
        $hasFile = Arr::get($item, 'file') instanceof UploadedFile;
        $errors = [];

        if (!$hasId && !$hasFile) {
            $errors["{$attribute}.{$index}.file"] = ['The file field is required when id is not present.'];
        }

        if ($hasId && !in_array((string) Arr::get($item, 'id'), $existingIds, true)) {
            $errors["{$attribute}.{$index}.id"] = ['The selected media item is invalid.'];
        }

        if ($hasId && $hasFile) {
            $errors["{$attribute}.{$index}.file"] = ['Omit the file when updating an existing media item.'];
        }

        if (array_key_exists('thumbnail', $item)) {
            $errors["{$attribute}.{$index}.thumbnail"] = ['The thumbnail field is only supported for images.'];
        }

        return $errors;
    }

    private function isSharedVariantOptionImage(mixed $src): bool
    {
        return is_string($src)
            && str_contains($src, '/variant_options/')
            && str_contains($src, '/images/');
    }
}
