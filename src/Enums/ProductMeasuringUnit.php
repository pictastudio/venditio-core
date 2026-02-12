<?php

namespace PictaStudio\Venditio\Enums;

enum ProductMeasuringUnit: string
{
    case Piece = 'pz';
    case Kilogram = 'kg';
    case Liter = 'lt';
    case Milliliter = 'ml';
    case Gram = 'g';
    case Meter = 'm';
    case SquareMeter = 'm2';
    case CubicMeter = 'm3';
    case Other = 'other';
}
