<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Support\Str;
use PictaStudio\Translatable\Locales;

trait SyncsTranslatedSlugs
{
    protected function syncTranslatedSlugs(bool $generateFromSource = true): void
    {
        if (!$this->supportsTranslatedSlugSync()) {
            return;
        }

        $currentLocale = $this->currentTranslatableLocale();
        $baseSlug = $this->normalizeTranslatedSlugValue(parent::getAttribute('slug'));

        if (
            $baseSlug !== ''
            && ($generateFromSource || $this->hasExplicitTranslatedSlugForLocale($currentLocale))
        ) {
            $this->setTranslationValue($currentLocale, 'slug', $baseSlug);
        }

        foreach ($this->translatedSlugLocales() as $locale) {
            if ($this->hasExplicitTranslatedSlugForLocale($locale)) {
                $explicitSlug = $locale === $currentLocale && $baseSlug !== ''
                    ? $baseSlug
                    : $this->normalizeTranslatedSlugValue($this->getTranslationValue($locale, 'slug'));

                if ($explicitSlug !== '') {
                    $this->setTranslationValue($locale, 'slug', $explicitSlug);
                }

                continue;
            }

            if (!$generateFromSource) {
                continue;
            }

            if ($locale === $currentLocale && $baseSlug !== '') {
                continue;
            }

            $source = $this->normalizeTranslatedSlugValue(
                $this->getTranslationValue($locale, $this->translatedSlugSourceAttribute())
            );

            if ($source === '') {
                continue;
            }

            $slug = Str::slug($source);

            if ($slug !== '') {
                $this->setTranslationValue($locale, 'slug', $slug);
            }
        }
    }

    protected function hasExplicitTranslatedSlugForLocale(string $locale): bool
    {
        return method_exists($this, 'hasExplicitSlugForLocale')
            && $this->hasExplicitSlugForLocale($locale);
    }

    protected function supportsTranslatedSlugSync(): bool
    {
        if (
            !method_exists($this, 'isTranslationAttribute')
            || !method_exists($this, 'setTranslationValue')
            || !method_exists($this, 'getTranslationValue')
        ) {
            return false;
        }

        if (!$this->isTranslationAttribute('slug')) {
            return false;
        }

        $sourceAttribute = $this->translatedSlugSourceAttribute();

        return $sourceAttribute !== '' && $this->isTranslationAttribute($sourceAttribute);
    }

    protected function translatedSlugSourceAttribute(): string
    {
        return 'name';
    }

    /**
     * @return array<int, string>
     */
    protected function translatedSlugLocales(): array
    {
        if (!app()->bound(Locales::class)) {
            return [app()->getLocale()];
        }

        return app(Locales::class)->all();
    }

    protected function currentTranslatableLocale(): string
    {
        if (!app()->bound(Locales::class)) {
            return app()->getLocale();
        }

        return app(Locales::class)->current();
    }

    protected function normalizeTranslatedSlugValue(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return mb_trim((string) $value);
    }
}
