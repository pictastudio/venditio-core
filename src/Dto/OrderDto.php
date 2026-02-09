<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;
use PictaStudio\VenditioCore\Models\Contracts\Cart;
use PictaStudio\VenditioCore\Models\Contracts\Order;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;

class OrderDto extends Dto implements OrderDtoContract
{
    public function __construct(
        private Model $order,
        private ?Model $cart,
        private ?int $userId,
        private ?string $userFirstName,
        private ?string $userLastName,
        private ?string $userEmail,
        private ?array $addresses,
        private ?string $customerNotes,
        private array $lines, // to swap with an order line dto

        // TODO: add the rest of the properties
        private ?float $subTotalTaxable = null,
        private ?float $subTotalTax = null,
        private ?float $subTotal = null,
        private ?float $shippingFee = null,
        private ?float $paymentFee = null,
        private ?string $discountCode = null,
        private ?float $discountAmount = null,
        private ?float $totalFinal = null,
    ) {

    }

    public static function fromCart(Model $cart): static
    {
        return new static(
            static::getFreshInstance(),
            $cart,
            $cart->user_id,
            $cart->user_first_name,
            $cart->user_last_name,
            $cart->user_email,
            $cart->addresses,
            $cart->notes,
            $cart->lines->toArray(),
            discountCode: $cart->discount_code,
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['order'] ?? static::getFreshInstance(),
            $data['cart'] ?? null,
            $data['user_id'] ?? null,
            $data['user_first_name'] ?? null,
            $data['user_last_name'] ?? null,
            $data['user_email'] ?? null,
            $data['addresses'] ?? null,
            $data['notes'] ?? null,
            $data['lines'] ?? [],
            discountCode: $data['discount_code'] ?? null,
        );
    }

    // public function toArray(): array
    // {
    //     return [
    //         'order' => $this->order,
    //         'cart' => $this->cart,
    //         'user_id' => $this->userId,
    //         'user_first_name' => $this->userFirstName,
    //         'user_last_name' => $this->userLastName,
    //         'user_email' => $this->userEmail,
    //         'discount_code' => $this->discountCode,
    //         'addresses' => $this->addresses,
    //         'notes' => $this->customerNotes,
    //         'lines' => $this->lines,
    //     ];
    // }

    // public function toCollection(): Collection
    // {
    //     return collect($this->toArray());
    // }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('order');
    }

    public function getModel(): Model
    {
        return $this->order;
    }

    public function getCart(): Model
    {
        return $this->cart;
    }

    public function getUserId(): ?int
    {
        return $this->userId ?? auth()->guard()->id();
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName ?? $this->getModel()?->user_first_name;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName ?? $this->getModel()?->user_last_name;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail ?? $this->getModel()?->user_email;
    }

    public function getDiscountCode(): ?string
    {
        return $this->discountCode ?? $this->getModel()?->discount_code;
    }

    public function getBillingAddress(): ?array
    {
        return $this->addresses['billing'] ?? $this->getModel()?->addresses['billing'] ?? null;
    }

    public function getShippingAddress(): ?array
    {
        return $this->addresses['shipping'] ?? $this->getModel()?->addresses['shipping'] ?? null;
    }

    public function getCustomerNotes(): ?string
    {
        return $this->customerNotes ?? $this->getModel()?->customer_notes ?? null;
    }

    /**
     * @return Collection<[['product_id' => int, 'qty' => int]]>
     */
    public function getLines(): Collection
    {
        return collect($this->lines);
    }

    // public static function bindIntoContainer(): static
    // {
    //     return new static(
    //         static::getFreshInstance(),
    //         app(CartDtoContract::class)::getFreshInstance(),
    //         null,
    //         null,
    //         null,
    //         null,
    //         null,
    //         null,
    //         null,
    //         null,
    //         [],
    //     );
    // }
}
