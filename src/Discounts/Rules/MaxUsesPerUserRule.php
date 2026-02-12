<?php

namespace PictaStudio\Venditio\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

use function PictaStudio\Venditio\Helpers\Functions\query;

class MaxUsesPerUserRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        $maxUsesPerUser = $discount->one_per_user
            ? 1
            : (int) ($discount->max_uses_per_user ?? 0);

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
