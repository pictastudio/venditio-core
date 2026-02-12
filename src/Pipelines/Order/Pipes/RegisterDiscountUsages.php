<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountUsageRecorderInterface;

class RegisterDiscountUsages
{
    public function __construct(
        private readonly DiscountUsageRecorderInterface $discountUsageRecorder,
    ) {}

    public function __invoke(Model $order, Closure $next): Model
    {
        $this->discountUsageRecorder->recordFromOrder($order);

        return $next($order);
    }
}
