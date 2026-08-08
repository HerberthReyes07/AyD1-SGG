<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\TrainerRepository;
use App\Models\Role;
use App\Models\TrainerSpecialty;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EmployeeService
{
    protected UserRepository $userRepository;
    protected TrainerRepository $trainerRepository;

    public function __construct(UserRepository $userRepository, TrainerRepository $trainerRepository)
    {
        $this->userRepository = $userRepository;
        $this->trainerRepository = $trainerRepository;
    }

    /**
     * Get all employees (trainers and receptionists).
     */
    public function getAllEmployees()
    {
        return $this->userRepository->getEmployees();
    }

    /**
     * Get active trainer specialties.
     */
    public function getTrainerSpecialties()
    {
        return TrainerSpecialty::where('is_active', true)->get();
    }

    /**
     * Get an employee by ID.
     */
    public function getEmployeeById(int|string $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create a new employee.
     */
    public function createEmployee(array $data)
    {
        $role = Role::findOrFail($data['role_id']);

        // if (!in_array($role->name, ['trainer', 'receptionist'])) {
        //     throw new InvalidArgumentException('Only trainers and receptionists can be created as employees.');
        // }

        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required.');
        }

        return DB::transaction(function () use ($data, $role) {
            $userData = [
                'role_id' => $role->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone_number' => $data['phone_number'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ];

            $user = $this->userRepository->create($userData);

            if ($role->name === 'trainer') {
                if (empty($data['specialty_id'])) {
                    throw new InvalidArgumentException('Trainer specialty is required.');
                }

                $specialty = TrainerSpecialty::findOrFail($data['specialty_id']);
                if (!$specialty->is_active) {
                    throw new InvalidArgumentException('Selected specialty is not active.');
                }

                $this->trainerRepository->create([
                    'user_id' => $user->id,
                    'specialty_id' => $specialty->id,
                ]);
            }

            return $user;
        });
    }

    /**
     * Update an existing employee.
     */
    public function updateEmployee(int|string $id, array $data)
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $role = $user->role;
        if (!in_array($role->name, ['trainer', 'receptionist'])) {
            throw new InvalidArgumentException('Only trainers and receptionists can be updated.');
        }

        if (isset($data['role_id'])) {
            $newRole = Role::findOrFail($data['role_id']);
            if ($newRole->name !== $role->name) {
                throw new InvalidArgumentException('Changing employee type after creation is not allowed.');
            }
        }

        return DB::transaction(function () use ($user, $data, $role) {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            $this->userRepository->update($user->id, $userData);

            if ($role->name === 'trainer') {
                if (empty($data['specialty_id'])) {
                    throw new InvalidArgumentException('Trainer specialty is required.');
                }

                $specialty = TrainerSpecialty::findOrFail($data['specialty_id']);
                if (!$specialty->is_active) {
                    throw new InvalidArgumentException('Selected specialty is not active.');
                }

                $this->trainerRepository->update($user->id, [
                    'specialty_id' => $specialty->id,
                ]);
            }

            return $user;
        });
    }

    /**
     * Deactivate an employee instead of deleting.
     */
    public function deleteEmployee(int|string $id)
    {
        return $this->userRepository->updateActiveStatus($id, false);
    }

    /**
     * Activate an employee instead of deleting.
     */

    public function activateEmployee(int|string $id)
    {
        return $this->userRepository->updateActiveStatus($id, true);
    }
}
