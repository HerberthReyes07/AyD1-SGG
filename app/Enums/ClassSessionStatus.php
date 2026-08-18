<?php

namespace App\Enums;

enum ClassSessionStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Programada',
            self::InProgress => 'En progreso',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
            self::Rescheduled => 'Reprogramada',
        };
    }
}
