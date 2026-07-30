<?php

namespace PictaStudio\Venditio\Validations;

use PictaStudio\Venditio\Validations\Concerns\{InteractsWithSlugRules, InteractsWithTranslatableRules};
use PictaStudio\Venditio\Validations\Contracts\ProductTypeValidationRules;

class ProductTypeValidation implements ProductTypeValidationRules
{
    use InteractsWithSlugRules;
    use InteractsWithTranslatableRules;

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['sometimes', 'filled', 'string', 'max:255'],
            'slug' => $this->slugRules('product_type'),
            'active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            ...$this->translatableLocaleRules([
                'name' => ['sometimes', 'filled', 'string', 'max:255'],
                'slug' => $this->slugRules('product_type'),
            ]),
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['sometimes', 'filled', 'string', 'max:255'],
            'slug' => $this->slugRules('product_type'),
            'active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            ...$this->translatableLocaleRules([
                'name' => ['sometimes', 'filled', 'string', 'max:255'],
                'slug' => $this->slugRules('product_type'),
            ]),
        ];
    }
}
