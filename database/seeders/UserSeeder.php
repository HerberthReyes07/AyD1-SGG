<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $receptionRole = Role::where('name', 'receptionist')->first();

        // Crear o actualizar usuario admin
        User::updateOrCreate(
            ['email' => 'admin@sgg.com'],
            [
                'role_id' => $adminRole->id ?? null,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('admin'),
                'phone_number' => null,
                'is_active' => true,
            ]
        );

        // Crear o actualizar usuario recepcionista
        User::updateOrCreate(
            ['email' => 'recep@sgg.com'],
            [
                'role_id' => $receptionRole->id ?? null,
                'first_name' => 'Receptionist',
                'last_name' => 'User',
                'password' => Hash::make('recep'),
                'phone_number' => null,
                'is_active' => true,
            ]
        );
    }
}
