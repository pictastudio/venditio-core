<?php

namespace PictaStudio\Venditio\Http\Requests\V1\Concerns;

use PictaStudio\Translatable\Locales;

trait InteractsWithTranslatableInput
{
    protected function prepareTranslatableInput(): void
    {
        $translations = $this->input('translations');

        if (!is_array($translations)) {
            return;
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $attributes) {
            if (!is_string($locale) || !is_array($attributes)) {
                continue;
            }

            $existingAttributes = $this->input($locale);

            if (is_array($existingAttributes)) {
                $preparedTranslations[$locale] = array_merge($attributes, $existingAttributes);

                continue;
            }

            $preparedTranslations[$locale] = $attributes;
        }

        if ($preparedTranslations === []) {
            return;
        }

        $this->merge($preparedTranslations);
    }

    protected function hasTranslatableValue(string $attribute): bool
    {
        if ($this->isFilledTranslatableValue($this->input($attribute))) {
            return true;
        }

        foreach ($this->translatableLocales() as $locale) {
            if ($this->isFilledTranslatableValue($this->input($attribute . ':' . $locale))) {
                return true;
            }

            if ($this->isFilledTranslatableValue(data_get($this->input($locale), $attribute))) {
                return true;
            }

            if ($this->isFilledTranslatableValue(data_get($this->input('translations'), $locale . '.' . $attribute))) {
                return true;
            }
        }

        return false;
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

    private function isFilledTranslatableValue(mixed $value): bool
    {
        if (is_string($value)) {
            return mb_trim($value) !== '';
        }

        return filled($value);
    }
}
