<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'class_enrollment_id',
    'check_in_at',
])]
class ClassAttendance extends Model
{
    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
        ];
    }

    public function classEnrollment(): BelongsTo
    {
        return $this->belongsTo(
            ClassEnrollment::class,
            'class_enrollment_id',
            'id'
        );
    }
}