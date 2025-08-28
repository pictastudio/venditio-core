<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Collection, Str};
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;

class CartDto extends Dto implements CartDtoContract
{
    public function __construct(
        private Model $cart,
        private ?int $userId,
        private ?string $userFirstName,
        private ?string $userLastName,
        private ?string $userEmail,
        private ?string $discountRef,
        private ?array $addresses,
        private ?Collection $lines = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            cart: $data['cart'] ?? static::getFreshInstance(),
            userId: $data['user_id'] ?? null,
            userFirstName: $data['user_first_name'] ?? null,
            userLastName: $data['user_last_name'] ?? null,
            userEmail: $data['user_email'] ?? null,
            discountRef: $data['discount_ref'] ?? null,
            addresses: $data['addresses'] ?? null,
            lines: collect($data['lines'] ?? [])
                ->map(fn (array $line) => CartLineDto::fromArray($line)),
        );
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('cart');
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $key => $value) {
            if (in_array($key, ['cart', 'lines'])) {
                continue;
            }

            $key = Str::snake($key);

            $data[$key] = $value;
        }

        return $data;
    }

    public function toModel(): Model
    {
        return $this->getCart()
            ->fill($this->toArray());
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
        return $this->userFirstName ?? $this->getCart()?->user_first_name;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName ?? $this->getCart()?->user_last_name;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail ?? $this->getCart()?->user_email;
    }

    public function getDiscountRef(): ?string
    {
        return $this->discountRef ?? $this->getCart()?->discount_code;
    }

    public function getAddresses(): ?array
    {
        return $this->addresses ?? $this->getCart()?->addresses;
    }

    /**
     * @return Collection<CartLineDto>
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }
}
