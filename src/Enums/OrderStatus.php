<?php

namespace PictaStudio\Venditio\Enums;

use PictaStudio\Venditio\Enums\Contracts\OrderStatus as OrderStatusContract;

enum OrderStatus: string implements OrderStatusContract
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Failed = 'failed';
    case OnHold = 'on_hold';
    case PaymentFailed = 'payment_failed';
    case PaymentPending = 'payment_pending';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public static function getProcessingStatus(): self
    {
        return self::Processing;
    }

    public static function getOnHoldStatus(): self
    {
        return self::OnHold;
    }

    public static function getCompletedStatus(): self
    {
        return self::Completed;
    }

    public static function getCancelledStatus(): self
    {
        return self::Cancelled;
    }

    public static function getNotConfirmedStatuses(): array
    {
        return [
            self::Pending,
            self::Processing,
            self::OnHold,
            self::PaymentFailed,
            self::PaymentPending,
        ];
    }
}
