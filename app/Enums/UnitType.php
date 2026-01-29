<?php

namespace App\Enums;

enum UnitType: string
{
    case KILOGRAM = 'kg';
    case METER = 'm';
    case CENTIMETER = 'cm';
    case UNITS = 'units';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::KILOGRAM => 'Kilograms',
            self::METER => 'Meters',
            self::CENTIMETER => 'Centimeters',
            self::UNITS => 'Units',
        };
    }
}