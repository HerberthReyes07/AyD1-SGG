<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class MembershipStatus extends Model
{
    public function memberships(): HasMany
    {
        return $this->hasMany(MemberMembership::class, 'status_id', 'id');
    }
}
