<?php

namespace PictaStudio\VenditioCore\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountRuleInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;
use PictaStudio\VenditioCore\Models\Discount;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class MaxUsesPerUserRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        $maxUsesPerUser = (int) $discount->getRule('max_uses_per_user', 0);

        if ($maxUsesPerUser <= 0) {
            return true;
        }

        $user = $context->getUser();

        if (!$user instanceof Model || blank($user->getKey())) {
            return false;
        }

        $userUsagesCount = query('discount_application')
            ->where('discount_id', $discount->getKey())
            ->where('user_id', $user->getKey())
            ->count();

        return $userUsagesCount < $maxUsesPerUser;
    }
}
