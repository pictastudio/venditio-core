<?php

namespace PictaStudio\Venditio\Enums\Contracts;

interface OrderStatus
{
    public static function getProcessingStatus(): self;

    public static function getOnHoldStatus(): self;

    public static function getCompletedStatus(): self;

    public static function getCancelledStatus(): self;

    public static function getNotConfirmedStatuses(): array;
}
