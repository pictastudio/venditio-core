<?php

namespace PictaStudio\Venditio\Actions\ProductCollections;

use Illuminate\Support\Arr;
use PictaStudio\Venditio\Models\ProductCollection;
use PictaStudio\Venditio\Support\CatalogImage;

class UpdateProductCollection
{
    public function handle(ProductCollection $collection, array $payload): ProductCollection
    {
        $imagesProvided = array_key_exists('images', $payload);
        $tagIdsProvided = array_key_exists('tag_ids', $payload);
        $productsProvided = array_key_exists('products', $payload);
        $images = Arr::pull($payload, 'images');
        $tagIds = Arr::pull($payload, 'tag_ids', []);
        $products = Arr::pull($payload, 'products', []);

        if ($imagesProvided) {
            $currentImages = CatalogImage::normalizeCollection($collection->getAttribute('images'));
            CatalogImage::validatePayload($images, CatalogImage::collectUsedIds($currentImages), 'images', $currentImages);

            $payload['images'] = CatalogImage::mergeCollection($collection, $currentImages, $images, 'product_collections');
        }

        $collection->fill($payload);
        $collection->save();

        if ($tagIdsProvided) {
            $collection->tags()->sync($tagIds ?? []);
        }

        if ($productsProvided) {
            $this->syncProducts($collection, $products ?? []);
        }

        return $collection->refresh()->load('tags');
    }

    private function syncProducts(ProductCollection $collection, array $products): void
    {
        $syncPayload = collect($products)
            ->mapWithKeys(fn (array $product): array => [
                (int) $product['id'] => ['sort_order' => (int) $product['sort_order']],
            ])
            ->all();

        $collection->products()->sync($syncPayload);
    }
}
