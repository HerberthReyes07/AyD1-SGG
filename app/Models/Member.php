<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'birth_date'])]
class Member extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(MemberMembership::class, 'member_id', 'user_id');
    }

    public function classEnrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'member_id', 'user_id');
    }

    public function classWaitlists(): HasMany
    {
        return $this->hasMany(ClassWaitlist::class, 'member_id', 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'member_id', 'user_id');
    }

    public function trainerAssignments(): HasMany
    {
        return $this->hasMany(TrainerAssignment::class, 'member_id', 'user_id');
    }
}
