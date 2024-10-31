<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Enums;

use PictaStudio\VenditioCore\Packages\Simple\Enums\Contracts\CartStatus as CartStatusContract;

enum CartStatus: string implements CartStatusContract
{
    case Processing = 'processing';
    case Active = 'active';
    case Converted = 'converted';
    case Abandoned = 'abandoned';
    case Cancelled = 'cancelled';

    public static function getProcessingStatus(): self
    {
        return self::Processing;
    }

    public static function getActiveStatus(): self
    {
        return self::Active;
    }

    public static function getConvertedStatus(): self
    {
        return self::Converted;
    }

    public static function getCancelledStatus(): self
    {
        return self::Cancelled;
    }

    public static function getInactiveStatuses(): array
    {
        return [
            self::Converted,
            self::Abandoned,
            self::Cancelled,
        ];
    }
}
