<?php

namespace PictaStudio\Venditio\Enums;

enum AddressType: string
{
    case Billing = 'billing';
    case Shipping = 'shipping';
}
