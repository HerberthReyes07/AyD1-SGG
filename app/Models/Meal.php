<?php

namespace App\Models;

use App\Enums\MealType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['date', 'type', 'member_id'])]
class Meal extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'type' => MealType::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function mealFoods(): HasMany
    {
        return $this->hasMany(MealFood::class, 'meal_id', 'id');
    }
}
