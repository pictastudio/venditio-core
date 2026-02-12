<?php

namespace PictaStudio\Venditio\Dto;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Dto\Contracts\CartLineDtoContract;
use PictaStudio\Venditio\Models\CartLine;

use function PictaStudio\Venditio\Helpers\Functions\{get_fresh_model_instance, resolve_dto};

class CartLineDto extends Dto implements CartLineDtoContract
{
    public function __construct(
        // private Model $cart,
        private Model $cartLine,
        private ?int $purchasableModelId,
        private int $qty,
    ) {}

    public static function fromArray(array $data): static
    {
        // $data['cart'] ??= resolve_dto('cart')::getFreshInstance();
        // $data['cart_line'] ??= static::getFreshInstance();

        // return parent::fromArray($data);

        return new static(
            // cart: resolve_dto('cart')::fromArray($data['cart']),
            cartLine: $data['cart_line'] ?? static::getFreshInstance(),
            purchasableModelId: $data['purchasable_model_id']
                ?? $data['product_id']
                ?? null,
            qty: $data['qty'] ?? 0,
        );
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('cart_line');
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    // public function getCart(): Model
    // {
    //     return $this->cart;
    // }

    public function getCartLine(): CartLine|Model
    {
        return $this->cartLine;
    }

    public function getPurchasableModelId(): ?int
    {
        return $this->purchasableModelId;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
}
