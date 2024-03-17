<?php

namespace PictaStudio\VenditioCore\Enums;

use PictaStudio\VenditioCore\Enums\Contracts\OrderStatus as OrderStatusContract;

enum OrderStatus: string implements OrderStatusContract
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
    case ON_HOLD = 'on_hold';
    case PAYMENT_FAILED = 'payment_failed';
    case PAYMENT_PENDING = 'payment_pending';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public static function getProcessingStatus(): self
    {
        return self::PROCESSING;
    }

    public static function getCompletedStatus(): self
    {
        return self::COMPLETED;
    }

    public static function getCancelledStatus(): self
    {
        return self::CANCELLED;
    }
}
