<?php

namespace PictaStudio\VenditioCore\Dto;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\BrandDtoContract;
use PictaStudio\VenditioCore\Models\Brand;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;

class BrandDto extends Dto implements BrandDtoContract
{
    public function __construct(
        private Model $brand,
        private ?string $name,
    ) {}

    public static function fromArray(array $data): static
    {
        $data['brand'] ??= static::getFreshInstance();

        return parent::fromArray($data);
    }

    public static function getFreshInstance(): Model
    {
        return get_fresh_model_instance('brand');
    }

    public function toModel(): Model
    {
        return $this->getFreshInstance()
            ->fill($this->toArray());
    }

    public function getBrand(): Brand|Model
    {
        return $this->brand;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function create(): Model
    {
        return $this->getBrand()->create([
            'name' => $this->getName(),
        ]);
    }

    public function update(): Model
    {
        $updatedData = [
            'name' => $this->getName(),
        ];

        $updatedData = array_filter($updatedData, fn ($value) => $value !== null);

        $this->getBrand()->update($updatedData);

        return $this->getBrand()->fill($updatedData);
    }
}
