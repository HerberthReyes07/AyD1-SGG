<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

    public function __construct()
    {
        //
    }

    /**
     * Get all members with their relations.
     */
    public function getMembers()
    {
        return User::whereHas('role', function ($query) {
            $query->whereIn('name', ['member']);
        })
        ->with(['role'])
        ->get();
    }

    /**
     * Get all employees (trainers and receptionists) with their relations.
     */
    public function getEmployees()
    {
        return User::whereHas('role', function ($query) {
            $query->whereIn('name', ['trainer', 'receptionist']);
        })
        ->with(['role', 'trainer.specialty'])
        ->get();
    }

    /**
     * Find a user by ID.
     */
    public function findById(int|string $id): ?User
    {
        return User::with(['role', 'trainer.specialty'])->find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     */
    public function update(int|string $id, array $data): bool
    {
        $user = User::findOrFail($id);
        return $user->update($data);
    }

    /**
     * Update is_active status.
     */
    public function updateActiveStatus(int|string $id, bool $isActive): bool
    {
        $user = User::findOrFail($id);
        $user->is_active = $isActive;
        return $user->save();
    }

    /**
     * Soft delete a user.
     */
    public function delete(int|string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
