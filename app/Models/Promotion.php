<?php

namespace App\Models;

use App\Enums\PromotionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'value', 'start_date', 'end_date', 'is_active', 'authorized_by'])]
class Promotion extends Model
{
    protected function casts(): array
    {
        return [
            'type' => PromotionType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'value' => 'decimal:2',
        ];
    }

    public function authorizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'promotion_id', 'id');
    }
}
