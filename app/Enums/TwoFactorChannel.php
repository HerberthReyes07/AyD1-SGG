<?php

namespace App\Enums;

//canal por el cual se enviara el codigo del 2fa
enum TwoFactorChannel: string
{
    case Email = 'email';
    case Sms = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Correo electrónico',
            self::Sms => 'Mensaje de texto (SMS)',
        };
    }
}