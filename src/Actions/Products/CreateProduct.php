<?php

namespace PictaStudio\Venditio\Actions\Products;

use Illuminate\Support\Arr;
use PictaStudio\Venditio\Contracts\ProductSkuGeneratorInterface;
use PictaStudio\Venditio\Models\Product;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class CreateProduct
{
    public function __construct(
        private readonly ProductSkuGeneratorInterface $productSkuGenerator,
    ) {}

    public function handle(array $payload): Product
    {
        $categoryIds = Arr::pull($payload, 'category_ids', []);
        $inventoryPayload = Arr::pull($payload, 'inventory');

        if (blank($payload['sku'] ?? null)) {
            $payload['sku'] = $this->productSkuGenerator->forProductPayload($payload);
        }

        /** @var Product $product */
        $product = resolve_model('product')::create($payload);

        if (!empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }

        if (is_array($inventoryPayload)) {
            $product->inventory()->updateOrCreate([], $inventoryPayload);
        }

        return $product->refresh()->load(['inventory', 'variantOptions']);
    }
}
