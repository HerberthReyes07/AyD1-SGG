<?php

namespace App\Enums;

enum Role: int
{
    case Admin = 1;
    case Receptionist = 2;
    case Trainer = 3;
    case Member = 4;

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Receptionist => 'Recepcionista',
            self::Trainer => 'Entrenador',
            self::Member => 'Socio',
        };
    }
}
