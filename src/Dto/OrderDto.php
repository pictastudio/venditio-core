<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Models\Cart;
use PictaStudio\VenditioCore\Models\Order;

final class OrderDto
{
    public function __construct(
        private Order $order,
        private ?Cart $cart,
        private ?int $userId,
        private string $userFirstName,
        private string $userLastName,
        private string $userEmail,
        private ?string $discountRef,
        private array $billingAddress,
        private array $shippingAddress,
        private ?string $customerNotes,
        private array $lines,
    ) {

    }

    public static function fromCart(Cart $cart): self
    {
        return new self(
            (self::getOrderInstance())->updateTimestamps(),
            $cart,
            $cart->user_id,
            $cart->user_first_name,
            $cart->user_last_name,
            $cart->user_email,
            $cart->discount_ref,
            $cart->addresses['billing'],
            $cart->addresses['shipping'],
            $cart->notes,
            $cart->lines->toArray(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['order'],
            $data['cart'] ?? null,
            $data['user_id'] ?? null,
            $data['user_first_name'] ?? null,
            $data['user_last_name'] ?? null,
            $data['user_email'] ?? null,
            $data['discount_ref'] ?? null,
            $data['billing_address'] ?? [],
            $data['shipping_address'] ?? [],
            $data['notes'] ?? [],
            $data['lines'] ?? [],
        );
    }

    public function getOrder(): Order
    {
        return $this->order;
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

    public function getCustomerNotes(): ?string
    {
        return $this->customerNotes;
    }

    /**
     * @return Collection<[['product_item_id' => int, 'qty' => int]]>
     */
    public function getLines(): Collection
    {
        return collect($this->lines);
    }

    private static function getOrderInstance(): Order
    {
        return app(config('venditio-core.models.order'));
    }
}
