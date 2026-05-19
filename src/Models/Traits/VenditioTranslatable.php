<?php

namespace PictaStudio\Venditio\Models\Traits;

use PictaStudio\Translatable\Translatable;

trait VenditioTranslatable
{
    use Translatable {
        shouldOverrideBaseColumnValue as protected translatableShouldOverrideBaseColumnValue;
    }

    protected function shouldOverrideBaseColumnValue(string $attribute, string $locale): bool
    {
        if ($locale === $this->locale()) {
            return true;
        }

        return $this->translatableShouldOverrideBaseColumnValue($attribute, $locale);
    }
}
