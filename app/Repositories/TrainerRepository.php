<?php

namespace App\Repositories;

use App\Models\Trainer;

class TrainerRepository
{
    public function __construct()
    {
        //
    }

    /**
     * Create a new trainer.
     */
    public function create(array $data): Trainer
    {
        return Trainer::create($data);
    }

    /**
     * Find a trainer by user ID.
     */
    public function findByUserId(int|string $userId): ?Trainer
    {
        return Trainer::find($userId);
    }

    /**
     * Update an existing trainer.
     */
    public function update(int|string $userId, array $data): bool
    {
        $trainer = Trainer::findOrFail($userId);
        return $trainer->update($data);
    }
}
