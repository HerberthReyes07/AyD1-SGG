<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'calories_per_serving', 'protein_g', 'carbs_g', 'fat_g', 'reference_serving_g', 'is_active', 'category_id'])]
class Food extends Model
{
    protected $table = 'foods';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'calories_per_serving' => 'decimal:2',
            'protein_g' => 'decimal:2',
            'carbs_g' => 'decimal:2',
            'fat_g' => 'decimal:2',
            'reference_serving_g' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FoodCategory::class, 'category_id', 'id');
    }

    public function mealFoods(): HasMany
    {
        return $this->hasMany(MealFood::class, 'food_id', 'id');
    }
}
