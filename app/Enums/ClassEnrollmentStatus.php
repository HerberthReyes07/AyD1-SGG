<?php

namespace App\Enums;

enum ClassEnrollmentStatus: string
{
    case Enrolled = 'enrolled';
    case Cancelled = 'cancelled';
    case Attended = 'attended';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Enrolled => 'Inscrito',
            self::Cancelled => 'Cancelado',
            self::Attended => 'Asistió',
            self::NoShow => 'No asistió',
        };
    }

}
