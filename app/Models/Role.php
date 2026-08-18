<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class Role extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    public function label(): string
    {
        return match ($this->name) {
            'admin' => 'Administrador',
            'receptionist' => 'Recepcionista',
            'trainer' => 'Entrenador',
            'member' => 'Socio',
            default => $this->name,
        };
    }
}
