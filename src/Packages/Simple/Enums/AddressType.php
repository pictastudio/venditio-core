<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Enums;

enum AddressType: string
{
    case Billing = 'billing';
    case Shipping = 'shipping';
}
