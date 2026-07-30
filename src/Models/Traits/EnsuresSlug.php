<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Support\SlugConfiguration;
use Spatie\Sluggable\SlugOptions;

trait EnsuresSlug
{
    protected static function bootEnsuresSlug(): void
    {
        static::saving(function (Model $model): void {
            if (!method_exists($model, 'generateSlug') || !method_exists($model, 'getSlugOptions')) {
                return;
            }

            $slugOptions = $model->getSlugOptions();
            $model->slugOptions = $slugOptions;
            $hasExplicitSlugInput = method_exists($model, 'hasExplicitSlugInput')
                && $model->hasExplicitSlugInput();
            $hasExplicitSlugForCurrentLocale = method_exists($model, 'hasExplicitSlugForCurrentLocale')
                && $model->hasExplicitSlugForCurrentLocale();

            if ($slugOptions->skipGenerate && !$hasExplicitSlugInput) {
                return;
            }

            $lifecycleAllowsGeneration = $model->exists
                ? $slugOptions->generateSlugsOnUpdate
                : $slugOptions->generateSlugsOnCreate;

            if (!$lifecycleAllowsGeneration && !$hasExplicitSlugInput) {
                return;
            }

            $slugField = $slugOptions->slugField;

            if ($hasExplicitSlugForCurrentLocale) {
                $explicitSlug = $model->normalizeSlugSource($model->{$slugField});

                if ($explicitSlug !== '' && method_exists($model, 'makeSlugUnique')) {
                    $model->{$slugField} = $model->makeSlugUnique($explicitSlug);
                }
            } elseif (
                $lifecycleAllowsGeneration
                && !$slugOptions->skipGenerate
                && (!$slugOptions->preventOverwrite || $model->{$slugField} === null)
                && $model->resolveSlugSourceForOptions($slugOptions) !== ''
            ) {
                $model->generateSlug();
            }

            if (method_exists($model, 'syncTranslatedSlugs')) {
                $model->syncTranslatedSlugs($lifecycleAllowsGeneration);
            }
        });
    }

    protected function venditioSlugOptions(SlugOptions $slugOptions, string $resource): SlugOptions
    {
        return SlugConfiguration::apply(
            $slugOptions,
            $resource,
            method_exists($this, 'hasExplicitSlugForCurrentLocale')
                && $this->hasExplicitSlugForCurrentLocale(),
        );
    }

    protected function resolveSlugSourceForOptions(SlugOptions $slugOptions): string
    {
        $source = $slugOptions->generateSlugFrom;

        if (is_callable($source)) {
            $value = call_user_func($source, $this);

            return $this->normalizeSlugSource($value);
        }

        if (!is_array($source)) {
            return '';
        }

        $values = [];
        foreach ($source as $fieldName) {
            if (!is_string($fieldName)) {
                continue;
            }

            $value = $this->normalizeSlugSource(data_get($this, $fieldName));
            if ($value !== '') {
                $values[] = $value;
            }
        }

        return implode($slugOptions->slugSeparator, $values);
    }

    protected function normalizeSlugSource(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return mb_trim((string) $value);
    }
}
