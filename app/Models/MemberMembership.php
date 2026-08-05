<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['member_id', 'plan_id', 'status_id', 'start_date', 'end_date', 'cancellation_reason', 'cancellation_date'])]
class MemberMembership extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'cancellation_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MembershipStatus::class, 'status_id', 'id');
    }

    public function freezes(): HasMany
    {
        return $this->hasMany(MembershipFreeze::class, 'member_membership_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'member_membership_id', 'id');
    }
}
