<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Dto\Contracts\CartLineDtoContract;
use PictaStudio\VenditioCore\Models\Contracts\CartLine;

class CartLineDto implements CartLineDtoContract
{
    public function __construct(
        private Model $cart,
        private Model $cartLine,
        private ?int $productItemId,
        private int $qty,
    ) {

    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['cart'] ?? app(CartDtoContract::class)::getInstance(),
            $data['cart_line'] ?? static::getInstance(),
            $data['product_item_id'] ?? null,
            $data['qty'] ?? 0,
        );
    }

    public function getCart(): Model
    {
        return $this->cart;
    }

    public function getCartLine(): CartLine|Model
    {
        return $this->cartLine;
    }

    public function getProductItemId(): ?int
    {
        return $this->productItemId;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public static function getInstance(): Model
    {
        return app(CartLine::class);
    }

    public static function bindIntoContainer(): static
    {
        return new static(
            app(CartDtoContract::class)::getInstance(),
            static::getInstance(),
            null,
            0,
        );
    }
}
