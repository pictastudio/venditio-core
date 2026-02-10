<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Collection, Fluent};
use PictaStudio\VenditioCore\Dto\Contracts\OrderDtoContract;

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
    ) {}

    public static function fromCart(Model $cart): static
    {
        $addresses = $cart->addresses;

        return new static(
            static::getFreshInstance(),
            $cart,
            $cart->user_id,
            $cart->user_first_name,
            $cart->user_last_name,
            $cart->user_email,
            $addresses instanceof Fluent ? $addresses->toArray() : $addresses,
            $cart->notes,
            $cart->lines->toArray(),
            $cart->sub_total_taxable,
            $cart->sub_total_tax,
            $cart->sub_total,
            $cart->shipping_fee,
            $cart->payment_fee,
            discountCode: $cart->discount_code,
            discountAmount: $cart->discount_amount,
            totalFinal: $cart->total_final,
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
            $data['sub_total_taxable'] ?? null,
            $data['sub_total_tax'] ?? null,
            $data['sub_total'] ?? null,
            $data['shipping_fee'] ?? null,
            $data['payment_fee'] ?? null,
            discountCode: $data['discount_code'] ?? null,
            discountAmount: $data['discount_amount'] ?? null,
            totalFinal: $data['total_final'] ?? null,
        );
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('order');
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->getUserId(),
            'user_first_name' => $this->getUserFirstName(),
            'user_last_name' => $this->getUserLastName(),
            'user_email' => $this->getUserEmail(),
            'sub_total_taxable' => $this->subTotalTaxable ?? $this->cart?->sub_total_taxable ?? 0,
            'sub_total_tax' => $this->subTotalTax ?? $this->cart?->sub_total_tax ?? 0,
            'sub_total' => $this->subTotal ?? $this->cart?->sub_total ?? 0,
            'shipping_fee' => $this->shippingFee ?? $this->cart?->shipping_fee ?? 0,
            'payment_fee' => $this->paymentFee ?? $this->cart?->payment_fee ?? 0,
            'discount_code' => $this->getDiscountCode(),
            'discount_amount' => $this->discountAmount ?? $this->cart?->discount_amount ?? 0,
            'total_final' => $this->totalFinal ?? $this->cart?->total_final ?? 0,
            'addresses' => $this->addresses
                ?? $this->normalizeAddresses($this->cart?->addresses)
                ?? [],
            'customer_notes' => $this->getCustomerNotes(),
        ];
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    public function getModel(): Model
    {
        return $this->order;
    }

    public function getCart(): Model
    {
        return $this->cart ?? get_fresh_model_instance('cart');
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

    private function normalizeAddresses(mixed $addresses): ?array
    {
        if (is_array($addresses)) {
            return $addresses;
        }

        if ($addresses instanceof Fluent) {
            return $addresses->toArray();
        }

        return null;
    }
}
