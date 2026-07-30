<?php

namespace PictaStudio\Venditio\Validations\Concerns;

use PictaStudio\Venditio\Support\SlugConfiguration;

trait InteractsWithSlugRules
{
    /**
     * @param  array<int, mixed>  $rules
     * @return array<int, mixed>
     */
    protected function slugRules(
        string $resource,
        array $rules = ['sometimes', 'filled', 'string', 'max:255'],
    ): array {
        if (!SlugConfiguration::editableViaApi($resource)) {
            return ['prohibited'];
        }

        return $rules;
    }
}
