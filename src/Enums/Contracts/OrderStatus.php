<?php

namespace PictaStudio\VenditioCore\Enums\Contracts;

interface OrderStatus
{
    public static function getProcessingStatus(): self;

    public static function getCompletedStatus(): self;

    public static function getCancelledStatus(): self;
}
