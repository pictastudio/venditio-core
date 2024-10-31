<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\Contracts\Cart;

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
        private array $lines = [],
    ) {

    }

    public static function fromArray(array $data): static
    {
        $dto = parent::fromArray($data);

        $dto->cart ??= static::getFreshInstance();

        return $dto;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $key => $value) {
            if (in_array($key, ['cart', 'lines'])) {
                continue;
            }

            $key = str($key)->snake()->toString();

            $data[$key] = $value;
        }

        return $data;
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());

        // return ($this->getCart() ?? $this->getFreshInstance())
        //     ->fill($this->toArray());
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('cart');
    }

    public function getCart(): Model
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
     * @return Collection<[['product_item_id' => int, 'qty' => int]]>
     */
    public function getLines(): Collection
    {
        return collect($this->lines);
    }
}
