<?php

namespace PictaStudio\VenditioCore\Enums\Contracts;

interface CartStatus
{
    public static function getProcessingStatus(): self;

    public static function getActiveStatus(): self;

    public static function getConvertedStatus(): self;

    public static function getCancelledStatus(): self;

    public static function getAbandonedStatus(): self;

    public static function getPendingStatuses(): array;

    public static function getCompletedStatuses(): array;

    public static function getInactiveStatuses(): array;
}
