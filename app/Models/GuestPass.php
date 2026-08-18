<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['guest_name', 'dpi', 'visit_date', 'registered_by'])]
class GuestPass extends Model
{
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by', 'id');
    }
}
