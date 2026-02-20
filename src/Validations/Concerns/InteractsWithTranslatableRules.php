<?php

namespace PictaStudio\Venditio\Validations\Concerns;

use PictaStudio\Translatable\Locales;

trait InteractsWithTranslatableRules
{
    protected function translatableLocaleRules(array $attributesRules): array
    {
        $rules = [];

        foreach ($this->translatableLocales() as $locale) {
            $rules[$locale] = ['sometimes', 'array'];

            foreach ($attributesRules as $attribute => $attributeRules) {
                $rules[$locale . '.' . $attribute] = $attributeRules;
                $rules[$attribute . ':' . $locale] = $attributeRules;
            }
        }

        return $rules;
    }

    protected function translatableLocales(): array
    {
        $locales = app(Locales::class)->all();

        if ($locales === []) {
            return [app()->getLocale()];
        }

        return collect($locales)
            ->filter(fn (mixed $locale): bool => is_string($locale) && filled($locale))
            ->values()
            ->all();
    }
}
