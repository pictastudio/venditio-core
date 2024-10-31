<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Packages\Simple\Models\CartLine;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_dto;

class CartLineDto extends Dto implements CartLineDtoContract
{
    public function __construct(
        private Model $cart,
        private Model $cartLine,
        private ?int $productId,
        private int $qty,
    ) {

    }

    public static function fromArray(array $data): static
    {
        $data['cart'] ??= resolve_dto('cart')::getFreshInstance();
        $data['cart_line'] ??= static::getFreshInstance();

        return parent::fromArray($data);
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('cart_line');
    }

    public function getCart(): Model
    {
        return $this->cart;
    }

    public function getCartLine(): CartLine|Model
    {
        return $this->cartLine;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
}
