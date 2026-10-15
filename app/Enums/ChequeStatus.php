<?php

namespace App\Enums;

enum ChequeStatus: string
{
    case InHand = 'in_hand';
    case Endorsed = 'endorsed';
    case Deposited = 'deposited';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::InHand => 'In hand',
            self::Endorsed => 'Given to supplier',
            self::Deposited => 'Deposited',
            self::Returned => 'Returned',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::InHand => 'info',
            self::Endorsed => 'warning',
            self::Deposited => 'success',
            self::Returned => 'danger',
        };
    }
}
