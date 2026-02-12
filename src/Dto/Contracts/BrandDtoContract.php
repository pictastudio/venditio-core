<?php

namespace PictaStudio\Venditio\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Models\Brand;

interface BrandDtoContract extends Dto
{
    public function getBrand(): Brand|Model;

    public function getName(): ?string;

    public function toModel(): Model;
}
