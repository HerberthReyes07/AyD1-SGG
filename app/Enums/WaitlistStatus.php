<?php

namespace App\Enums;

enum WaitlistStatus: string
{
    case Waiting = 'waiting';
    case Notified = 'notified';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Esperando',
            self::Notified => 'Notificado',
            self::Expired => 'Expirado',
        };
    }
}
