<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class ClassCategory extends Model
{
    public function groupClasses(): HasMany
    {
        return $this->hasMany(GroupClass::class, 'category_id', 'id');
    }
}
