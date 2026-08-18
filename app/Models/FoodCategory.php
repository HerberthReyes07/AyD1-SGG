<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class FoodCategory extends Model
{
    public function foods(): HasMany
    {
        return $this->hasMany(Food::class, 'category_id', 'id');
    }
}
