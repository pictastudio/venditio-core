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
        private array $billingAddress,
        private array $shippingAddress,
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
            $data['billing_address'] ?? [],
            $data['shipping_address'] ?? [],
            $data['notes'] ?? [],
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
        return $this->userFirstName;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function getUserEmail(): ?string
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
            [],
            [],
            null,
            [],
        );
    }
}
