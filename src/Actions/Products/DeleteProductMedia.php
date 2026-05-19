<?php

namespace PictaStudio\Venditio\Actions\Products;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\{Storage, URL};
use PictaStudio\Venditio\Models\Product;
use PictaStudio\Venditio\Support\ProductMedia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class DeleteProductMedia
{
    public function handle(Product $product, string $mediaId): Product
    {
        $files = ProductMedia::normalizeCollection($product->getAttribute('files'), isImage: false);

        $deletedMedia = null;
        [$files, $deletedMedia] = $this->removeFromCollection($files, $mediaId);

        if ($deletedMedia === null) {
            throw new NotFoundHttpException('Product media not found.');
        }

        $product->forceFill([
            'files' => $files,
        ]);
        $product->save();

        if ($this->shouldDeleteFromFilesystem()) {
            $this->deleteFromFilesystem(Arr::get($deletedMedia, 'src'), $product);
        }

        return $product->refresh();
    }

    /**
     * @param  array<int, array{id: string}>  $items
     * @return array{0: array<int, array{id: string}>, 1: array<string, mixed>|null}
     */
    private function removeFromCollection(array $items, string $mediaId): array
    {
        $deletedMedia = null;

        $items = collect($items)
            ->reject(function (array $item) use ($mediaId, &$deletedMedia): bool {
                $matches = Arr::get($item, 'id') === $mediaId;

                if ($matches) {
                    $deletedMedia = $item;
                }

                return $matches;
            })
            ->values()
            ->all();

        return [$items, $deletedMedia];
    }

    private function shouldDeleteFromFilesystem(): bool
    {
        return (bool) config('venditio.product.media.delete_files_from_filesystem', true);
    }

    private function deleteFromFilesystem(mixed $path, Product $deletedFromProduct): void
    {
        if (!is_string($path) || blank($path) || URL::isValidUrl($path)) {
            return;
        }

        if ($this->isReferencedByAnotherProduct($path, $deletedFromProduct)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function isReferencedByAnotherProduct(string $path, Product $deletedFromProduct): bool
    {
        $productModelClass = resolve_model('product');

        return $productModelClass::withoutGlobalScopes()
            ->whereKeyNot($deletedFromProduct->getKey())
            ->get(['files'])
            ->contains(function (Product $product) use ($path): bool {
                return collect(ProductMedia::normalizeCollection($product->getAttribute('files'), isImage: false))
                    ->contains(fn (array $item): bool => Arr::get($item, 'src') === $path);
            });
    }
}
