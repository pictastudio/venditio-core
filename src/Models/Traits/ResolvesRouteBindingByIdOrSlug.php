<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Database\Eloquent\{Builder, Model};
use PictaStudio\Venditio\Support\NonSoftDeletingScopeExcluder;

trait ResolvesRouteBindingByIdOrSlug
{
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field !== null) {
            if ($field === $this->getKeyName() && !$this->isCanonicalRouteKey($value)) {
                return null;
            }

            return $this->resolveRouteBindingQuery(
                $this->newRouteBindingQuery(),
                $value,
                $field
            )->first();
        }

        $model = $this->isCanonicalRouteKey($value)
            ? $this->resolveRouteBindingQuery(
                $this->newRouteBindingQuery(),
                $value,
                $this->getKeyName()
            )->first()
            : null;

        if ($model !== null) {
            return $model;
        }

        $model = $this->resolveRouteBindingQuery(
            $this->newRouteBindingQuery(),
            $value,
            'slug'
        )->first();

        if ($model !== null) {
            return $model;
        }

        return $this->resolveByTranslatedSlug($value);
    }

    private function isCanonicalRouteKey(mixed $value): bool
    {
        if (is_int($value)) {
            return $value > 0;
        }

        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[1-9][0-9]*$/', $value) === 1;
    }

    private function resolveByTranslatedSlug(mixed $value): ?Model
    {
        if (
            !method_exists($this, 'getTranslationModelName')
            || !method_exists($this, 'getLocaleKey')
            || !method_exists($this, 'getMorphClass')
        ) {
            return null;
        }

        $translationModel = $this->getTranslationModelName();
        if (!is_string($translationModel) || !is_a($translationModel, Model::class, true)) {
            return null;
        }

        $localeColumn = $this->getLocaleKey();
        if (!is_string($localeColumn) || $localeColumn === '') {
            $localeColumn = 'locale';
        }

        $translatedId = $translationModel::query()
            ->where('translatable_type', $this->getMorphClass())
            ->where('attribute', 'slug')
            ->where('value', (string) $value)
            ->when(
                app()->getLocale(),
                fn (Builder $query, string $locale) => $query->where($localeColumn, $locale)
            )
            ->value('translatable_id');

        if ($translatedId === null) {
            $translatedId = $translationModel::query()
                ->where('translatable_type', $this->getMorphClass())
                ->where('attribute', 'slug')
                ->where('value', (string) $value)
                ->value('translatable_id');
        }

        if ($translatedId === null) {
            return null;
        }

        return $this->resolveRouteBindingQuery(
            $this->newRouteBindingQuery(),
            $translatedId,
            $this->getKeyName()
        )->first();
    }

    private function newRouteBindingQuery(): Builder
    {
        $query = $this->newQuery();

        if (request()->boolean('exclude_all_scopes')) {
            return NonSoftDeletingScopeExcluder::apply($query);
        }

        return $query;
    }
}
