<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Translatable\Locales;

trait TracksExplicitSlugInput
{
    protected bool $venditioExplicitBaseSlug = false;

    /**
     * @var array<string, bool>
     */
    protected array $venditioExplicitSlugLocales = [];

    protected static function bootTracksExplicitSlugInput(): void
    {
        static::saved(function (Model $model): void {
            $model->clearExplicitSlugInput();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function rememberExplicitSlugInput(array $attributes): void
    {
        if (array_key_exists('slug', $attributes)) {
            $this->venditioExplicitBaseSlug = true;
            $this->venditioExplicitSlugLocales[$this->venditioSlugInputLocale()] = true;
        }

        foreach ($attributes as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (str_starts_with($key, 'slug:')) {
                $locale = mb_substr($key, 5);

                if ($locale !== '') {
                    $this->venditioExplicitSlugLocales[$locale] = true;
                }

                continue;
            }

            if ($this->isVenditioLocale($key) && is_array($value) && array_key_exists('slug', $value)) {
                $this->venditioExplicitSlugLocales[$key] = true;
            }
        }

        $translationsWrapper = config('translatable.translations_wrapper');

        if (
            is_string($translationsWrapper)
            && $translationsWrapper !== ''
            && isset($attributes[$translationsWrapper])
            && is_array($attributes[$translationsWrapper])
        ) {
            $this->rememberExplicitSlugTranslations($attributes[$translationsWrapper]);
        }
    }

    protected function hasExplicitSlugInput(): bool
    {
        return $this->venditioExplicitBaseSlug || $this->venditioExplicitSlugLocales !== [];
    }

    protected function hasExplicitSlugForCurrentLocale(): bool
    {
        return $this->venditioExplicitBaseSlug
            || $this->hasExplicitSlugForLocale($this->venditioSlugInputLocale());
    }

    protected function hasExplicitSlugForLocale(string $locale): bool
    {
        return ($this->venditioExplicitSlugLocales[$locale] ?? false) === true;
    }

    protected function clearExplicitSlugInput(): void
    {
        $this->venditioExplicitBaseSlug = false;
        $this->venditioExplicitSlugLocales = [];
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    private function rememberExplicitSlugTranslations(array $translations): void
    {
        foreach ($translations as $locale => $values) {
            if (is_string($locale) && is_array($values) && array_key_exists('slug', $values)) {
                $this->venditioExplicitSlugLocales[$locale] = true;
            }
        }
    }

    private function venditioSlugInputLocale(): string
    {
        if (method_exists($this, 'locale')) {
            return $this->locale();
        }

        if (app()->bound(Locales::class)) {
            return app(Locales::class)->current();
        }

        return app()->getLocale();
    }

    private function isVenditioLocale(string $locale): bool
    {
        if (!app()->bound(Locales::class)) {
            return $locale === app()->getLocale();
        }

        return app(Locales::class)->has($locale);
    }
}
