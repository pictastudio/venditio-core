<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Models\Contracts\Cart;
use PictaStudio\VenditioCore\Models\Contracts\Order;

class OrderDto implements OrderDtoContract
{
    public function __construct(
        private Model $order,
        private ?Model $cart,
        private ?int $userId,
        private ?string $userFirstName,
        private ?string $userLastName,
        private ?string $userEmail,
        private ?string $discountRef,
        private ?array $billingAddress,
        private ?array $shippingAddress,
        private ?string $customerNotes,
        private array $lines,
    ) {

    }

    public static function fromCart(Model $cart): static
    {
        return new static(
            static::getInstance(),
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

    public static function fromArray(array $data): static
    {
        return new static(
            $data['order'] ?? static::getInstance(),
            $data['cart'] ?? null,
            $data['user_id'] ?? null,
            $data['user_first_name'] ?? null,
            $data['user_last_name'] ?? null,
            $data['user_email'] ?? null,
            $data['discount_ref'] ?? null,
            $data['billing_address'] ?? null,
            $data['shipping_address'] ?? null,
            $data['notes'] ?? null,
            $data['lines'] ?? [],
        );
    }

    public function getOrder(): Order|Model
    {
        return $this->order;
    }

    public function getCart(): Cart|Model
    {
        return $this->cart;
    }

    public function getUserId(): ?int
    {
        return $this->userId ?? auth()->id();
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName ?? $this->getOrder()?->user_first_name;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName ?? $this->getOrder()?->user_last_name;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail ?? $this->getOrder()?->user_email;
    }

    public function getDiscountRef(): ?string
    {
        return $this->discountRef ?? $this->getOrder()?->discount_ref;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress ?? $this->getOrder()?->addresses['billing'] ?? null;
    }

    public function getShippingAddress(): ?array
    {
        return $this->shippingAddress ?? $this->getOrder()?->addresses['shipping'] ?? null;
    }

    public function getCustomerNotes(): ?string
    {
        return $this->customerNotes ?? $this->getOrder()?->customer_notes ?? null;
    }

    /**
     * @return Collection<[['product_item_id' => int, 'qty' => int]]>
     */
    public function getLines(): Collection
    {
        return collect($this->lines);
    }

    public static function getInstance(): Model
    {
        return app(Order::class);
    }

    public static function bindIntoContainer(): static
    {
        return new static(
            static::getInstance(),
            app(CartDtoContract::class)::getInstance(),
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            [],
        );
    }
}
