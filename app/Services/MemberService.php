<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Member;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MemberService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all members
     */
    public function getAllMembers()
    {
        return $this->userRepository->getMembers();
    }

    /**
     * Get a member by ID.
     */
    public function getMemberById(int|string $id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create a new member.
     */
    public function createMember(array $data)
    {
        $role = Role::where('name', 'member')->firstOrFail();

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

            Member::create([
                'user_id' => $user->id,
                'birth_date' => $data['birth_date'],
            ]);

            return $user;
        });
    }

    /**
     * Update an existing member.
     */
    public function updateMember(int|string $id, array $data)
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $role = $user->role;
        if (!in_array($role->name, ['member'])) {
            throw new InvalidArgumentException('Only members can be updated.');
        }

        if (isset($data['role_id'])) {
            $newRole = Role::findOrFail($data['role_id']);
            if ($newRole->name !== $role->name) {
                throw new InvalidArgumentException('Changing member type after creation is not allowed.');
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

            if (isset($data['birth_date'])) {
                $user->member()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['birth_date' => $data['birth_date']]
                );
            }

            return $user;
        });
    }

    /**
     * Deactivate an member instead of deleting.
     */
    public function deleteMember(int|string $id)
    {
        return $this->userRepository->updateActiveStatus($id, false);
    }

    /**
     * Activate an member instead of deleting.
     */

    public function activateMember(int|string $id)
    {
        return $this->userRepository->updateActiveStatus($id, true);
    }
}
