<?php

namespace PictaStudio\VenditioCore\Validations\Contracts;

interface ProvidesValidationRules
{
    public function getStoreValidationRules(): array;

    public function getUpdateValidationRules(): array;
}
