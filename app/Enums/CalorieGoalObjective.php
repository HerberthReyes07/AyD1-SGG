<?php

namespace App\Enums;

enum CalorieGoalObjective: string
{
    case LoseWeight = 'lose_weight';
    case GainMuscle = 'gain_muscle';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::LoseWeight => 'Bajar de peso',
            self::GainMuscle => 'Ganar masa muscular',
            self::Maintenance => 'Mantenimiento',
        };
    }
}
