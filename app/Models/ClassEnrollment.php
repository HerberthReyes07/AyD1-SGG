<?php

namespace App\Models;

use App\Enums\ClassEnrollmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['enrollment_date', 'member_id', 'status', 'class_session_id'])]
class ClassEnrollment extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ClassEnrollmentStatus::class,
            'enrollment_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id');
    }

    public function classRatings(): HasMany
    {
        return $this->hasMany(ClassRating::class, 'class_enrollment_id', 'id');
    }

}
