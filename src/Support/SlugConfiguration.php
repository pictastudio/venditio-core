<?php

namespace PictaStudio\Venditio\Support;

use Spatie\Sluggable\SlugOptions;

final class SlugConfiguration
{
    public static function regenerateOnUpdate(string $resource): bool
    {
        return self::booleanValue($resource, 'regenerate_on_update', true);
    }

    public static function editableViaApi(string $resource): bool
    {
        return self::booleanValue($resource, 'editable_via_api', true);
    }

    public static function apply(
        SlugOptions $slugOptions,
        string $resource,
        bool $explicitSlugProvided = false,
    ): SlugOptions {
        if (!self::regenerateOnUpdate($resource)) {
            $slugOptions->doNotGenerateSlugsOnUpdate();
        }

        if ($explicitSlugProvided) {
            $slugOptions->skipGenerate = true;
        }

        return $slugOptions;
    }

    private static function booleanValue(string $resource, string $key, bool $default): bool
    {
        $resourceConfiguration = config("venditio.slugs.resources.{$resource}", []);

        if (is_array($resourceConfiguration) && array_key_exists($key, $resourceConfiguration)) {
            return (bool) $resourceConfiguration[$key];
        }

        return (bool) config("venditio.slugs.{$key}", $default);
    }
}
