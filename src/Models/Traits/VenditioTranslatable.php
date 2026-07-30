<?php

namespace PictaStudio\Venditio\Models\Traits;

use PictaStudio\Translatable\Translatable;

trait VenditioTranslatable
{
    use TracksExplicitSlugInput;
    use Translatable {
        fill as protected fillTranslatableAttributes;
        shouldOverrideBaseColumnValue as protected translatableShouldOverrideBaseColumnValue;
    }

    public function fill(array $attributes)
    {
        $this->rememberExplicitSlugInput($attributes);

        return $this->fillTranslatableAttributes($attributes);
    }

    protected function shouldOverrideBaseColumnValue(string $attribute, string $locale): bool
    {
        if ($locale === $this->locale()) {
            return true;
        }

        return $this->translatableShouldOverrideBaseColumnValue($attribute, $locale);
    }
}
