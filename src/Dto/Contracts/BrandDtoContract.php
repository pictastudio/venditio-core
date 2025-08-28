<?php

namespace PictaStudio\VenditioCore\Dto\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;

interface BrandDtoContract extends Dto
{
    public function getBrand(): Brand|Model;

    public function getName(): ?string;

    public function toModel(): Model;
}
