<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\{Arr, Str};
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    private const TYPES = ['thumb', 'cover'];

    public function up(): void
    {
        if (!Schema::hasColumn('products', 'images')) {
            return;
        }

        DB::table('products')
            ->select(['id', 'images'])
            ->orderBy('id')
            ->get()
            ->each(function (object $record): void {
                $images = $this->normalizeImages($record->images ?? null);

                DB::table('products')
                    ->where('id', $record->id)
                    ->update([
                        'images' => $images === []
                            ? null
                            : json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ]);
            });
    }

    public function down(): void
    {
        // Legacy product image metadata cannot be reconstructed after normalization.
    }

    private function normalizeImages(mixed $value): array
    {
        $usedIds = [];
        $seenTypes = [];

        $images = collect($this->decodeJson($value))
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values()
            ->map(function (array $item, int $index) use (&$usedIds, &$seenTypes): ?array {
                $src = Arr::get($item, 'src');

                if (!is_string($src) || blank($src)) {
                    return null;
                }

                $shared = $this->isSharedVariantOptionImage($src)
                    || filter_var(Arr::get($item, 'shared_from_variant_option'), FILTER_VALIDATE_BOOL);
                $type = $shared ? null : $this->resolveType(Arr::get($item, 'type'));

                if ($type !== null) {
                    if (array_key_exists($type, $seenTypes)) {
                        $type = null;
                    } else {
                        $seenTypes[$type] = true;
                    }
                }

                $sortOrderIsDefault = !is_numeric(Arr::get($item, 'sort_order'))
                    || (int) Arr::get($item, 'sort_order') < 0;

                return [
                    'id' => $this->resolveUniqueId(Arr::get($item, 'id'), $usedIds),
                    'type' => $type,
                    'name' => Arr::get($item, 'name'),
                    'alt' => Arr::get($item, 'alt'),
                    'mimetype' => Arr::get($item, 'mimetype'),
                    'src' => $src,
                    'sort_order' => $this->resolveSortOrder(
                        Arr::get($item, 'sort_order'),
                        $this->defaultSortOrder($type, $index)
                    ),
                    '_legacy_thumbnail' => filter_var(Arr::get($item, 'thumbnail'), FILTER_VALIDATE_BOOL),
                    '_original_index' => $index,
                    '_shared' => $shared,
                    '_sort_order_is_default' => $sortOrderIsDefault,
                    '_type_weight' => $this->sortWeight($type),
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (!array_key_exists('thumb', $seenTypes)) {
            $this->assignFirstAvailableType($images, 'thumb', fn (array $image): bool => (
                !(bool) Arr::get($image, '_shared')
                && (bool) Arr::get($image, '_legacy_thumbnail')
                && Arr::get($image, 'type') === null
            ));
        }

        if (!collect($images)->contains(fn (array $image): bool => Arr::get($image, 'type') === 'cover')) {
            $this->assignFirstAvailableType($images, 'cover', fn (array $image): bool => (
                !(bool) Arr::get($image, '_shared')
                && Arr::get($image, 'type') === null
            ));
        }

        return collect($images)
            ->sortBy([
                ['sort_order', 'asc'],
                ['_type_weight', 'asc'],
                ['id', 'asc'],
            ])
            ->map(fn (array $image): array => Arr::except($image, [
                '_legacy_thumbnail',
                '_original_index',
                '_shared',
                '_sort_order_is_default',
                '_type_weight',
            ]))
            ->values()
            ->all();
    }

    private function assignFirstAvailableType(array &$images, string $type, callable $matches): void
    {
        foreach ($images as &$image) {
            if (!$matches($image)) {
                continue;
            }

            $image['type'] = $type;
            $image['_type_weight'] = $this->sortWeight($type);

            if ((bool) Arr::get($image, '_sort_order_is_default')) {
                $image['sort_order'] = $this->defaultSortOrder($type, (int) Arr::get($image, '_original_index', 0));
            }

            break;
        }
    }

    private function decodeJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || blank($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function resolveType(mixed $type): ?string
    {
        if (!is_string($type) || blank($type)) {
            return null;
        }

        $normalized = mb_strtolower(mb_trim($type));

        return in_array($normalized, self::TYPES, true) ? $normalized : null;
    }

    private function resolveSortOrder(mixed $value, int $default): int
    {
        if (is_numeric($value) && (int) $value >= 0) {
            return (int) $value;
        }

        return $default;
    }

    private function defaultSortOrder(mixed $type, int $index = 0): int
    {
        $type = $this->resolveType($type);

        return $type === null
            ? count(self::TYPES) + $index
            : $this->sortWeight($type);
    }

    private function sortWeight(mixed $type): int
    {
        $index = array_search($this->resolveType($type), self::TYPES, true);

        return is_int($index) ? $index : count(self::TYPES);
    }

    private function resolveUniqueId(mixed $id, array &$usedIds): string
    {
        if (is_scalar($id)) {
            $candidate = (string) $id;

            if (filled($candidate) && !in_array($candidate, $usedIds, true)) {
                $usedIds[] = $candidate;

                return $candidate;
            }
        }

        do {
            $id = (string) Str::ulid();
        } while (in_array($id, $usedIds, true));

        $usedIds[] = $id;

        return $id;
    }

    private function isSharedVariantOptionImage(mixed $src): bool
    {
        return is_string($src)
            && str_contains($src, '/variant_options/')
            && str_contains($src, '/images/');
    }
};
