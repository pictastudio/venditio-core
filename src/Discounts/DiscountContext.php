<?php

namespace PictaStudio\VenditioCore\Discounts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Models\Discount;

class DiscountContext
{
    /**
     * @var array<string, bool>
     */
    private array $appliedDiscounts = [];

    public function __construct(
        private readonly ?Model $cart = null,
        private readonly ?Model $order = null,
        private readonly ?Model $user = null,
    ) {}

    public static function make(?Model $cart = null, ?Model $order = null, ?Model $user = null): self
    {
        return new self($cart, $order, $user);
    }

    public function getCart(): ?Model
    {
        return $this->cart;
    }

    public function getOrder(): ?Model
    {
        return $this->order;
    }

    public function getUser(): ?Model
    {
        return $this->user;
    }

    public function hasDiscountBeenAppliedInCart(Discount $discount): bool
    {
        return isset($this->appliedDiscounts[$this->getDiscountKey($discount)]);
    }

    public function markDiscountAsAppliedInCart(Discount $discount): void
    {
        $this->appliedDiscounts[$this->getDiscountKey($discount)] = true;
    }

    private function getDiscountKey(Discount $discount): string
    {
        return implode(':', [
            $discount->getMorphClass(),
            (string) $discount->getKey(),
        ]);
    }
}
