<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['rating', 'comment', 'class_enrollment_id'])]
class ClassRating extends Model
{
    public function classEnrollment(): BelongsTo
    {
        return $this->belongsTo(ClassEnrollment::class, 'class_enrollment_id', 'id');
    }
}
