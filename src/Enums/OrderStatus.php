<?php

namespace PictaStudio\VenditioCore\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
    case ON_HOLD = 'on_hold';
    case AWAITING_PAYMENT = 'awaiting_payment';
}
