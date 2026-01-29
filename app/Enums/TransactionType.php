<?php

namespace App\Enums;

enum TransactionType: string
{
    case ADDITION = 'addition';
    case DEDUCTION = 'deduction';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match($this) {
            self::ADDITION => 'green',
            self::DEDUCTION => 'red',
        };
    }
}