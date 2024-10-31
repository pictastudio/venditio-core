<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Enums\Contracts;

interface CartStatus
{
    public static function getProcessingStatus(): self;

    public static function getActiveStatus(): self;

    public static function getConvertedStatus(): self;

    public static function getCancelledStatus(): self;

    public static function getInactiveStatuses(): array;
}
