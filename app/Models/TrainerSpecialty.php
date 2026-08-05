<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class TrainerSpecialty extends Model
{
    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class, 'specialty_id', 'id');
    }
}
