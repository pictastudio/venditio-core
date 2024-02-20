<?php

namespace PictaStudio\VenditioCore\Enums;

enum ProductMeasuringUnit: string
{
    case PIECE = 'pz';
    case KILOGRAM = 'kg';
    case LITER = 'lt';
    case MILLILITER = 'ml';
    case GRAM = 'g';
    case METER = 'm';
    case SQUARE_METER = 'm2';
    case CUBIC_METER = 'm3';
    case HOUR = 'h';
    case DAY = 'd';
    case WEEK = 'w';
    case MONTH = 'm';
    case YEAR = 'y';
    case OTHER = 'other';
}
