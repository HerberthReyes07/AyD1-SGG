<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'price', 'duration_months', 'includes_group_classes', 'weekly_class_limit', 'includes_trainer', 'has_waitlist_priority'])]
class MembershipPlan extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'includes_group_classes' => 'boolean',
            'includes_trainer' => 'boolean',
            'has_waitlist_priority' => 'boolean',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(MemberMembership::class, 'plan_id', 'id');
    }
}
