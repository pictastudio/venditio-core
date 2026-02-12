<?php

namespace PictaStudio\Venditio\Validations\Contracts;

interface ProvidesValidationRules
{
    public function getStoreValidationRules(): array;

    public function getUpdateValidationRules(): array;
}
