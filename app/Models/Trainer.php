<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'specialty_id'])]
class Trainer extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(TrainerSpecialty::class, 'specialty_id', 'id');
    }
}
