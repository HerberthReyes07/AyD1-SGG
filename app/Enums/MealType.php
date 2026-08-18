<?php

namespace App\Enums;

enum MealType: string
{
    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';
    case Snack = 'snack';

    public function label(): string
    {
        return match ($this) {
            self::Breakfast => 'Desayuno',
            self::Lunch => 'Almuerzo',
            self::Dinner => 'Cena',
            self::Snack => 'Snack',
        };
    }
}
