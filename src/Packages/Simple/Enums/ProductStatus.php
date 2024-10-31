<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
