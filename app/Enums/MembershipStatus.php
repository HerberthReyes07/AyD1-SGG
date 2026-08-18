<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case Active = 'active';
    case Frozen = 'frozen';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activa',
            self::Frozen => 'Congelada',
            self::Expired => 'Vencida',
            self::Cancelled => 'Cancelada',
        };
    }
}
