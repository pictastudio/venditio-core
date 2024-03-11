<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Models\Cart;

final class CartDto
{
    public function __construct(
        private Cart $cart,
        private ?int $userId,
        private string $userFirstName,
        private string $userLastName,
        private string $userEmail,
        private ?string $discountRef,
        private array $billingAddress,
        private array $shippingAddress,
        private array $lines,
    ) {

    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cart'],
            $data['user_id'] ?? null,
            $data['user_first_name'] ?? null,
            $data['user_last_name'] ?? null,
            $data['user_email'] ?? null,
            $data['discount_ref'] ?? null,
            $data['billing_address'] ?? [],
            $data['shipping_address'] ?? [],
            $data['lines'] ?? [],
        );
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getUserId(): ?int
    {
        return $this->userId ?? auth()->id();
    }

    public function getUserFirstName(): string
    {
        return $this->userFirstName;
    }

    public function getUserLastName(): string
    {
        return $this->userLastName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getDiscountRef(): ?string
    {
        return $this->discountRef;
    }

    public function getBillingAddress(): array
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): array
    {
        return $this->shippingAddress;
    }

    /**
     * @return Collection<[['product_item_id' => int, 'qty' => int]]>
     */
    public function getLines(): Collection
    {
        return collect($this->lines);
    }
}
