<?php

namespace Database\Seeders;

use App\Enums\MembershipStatus;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestMemberSeeder extends Seeder
{
    public function run(): void
    {
        $memberRole = Role::where('name', 'member')->firstOrFail();
        $elitePlan = MembershipPlan::where('name', 'Elite - Monthly')->firstOrFail();

        $user = User::firstOrCreate(
            ['email' => 'socio5@test.com'],
            [
                'role_id' => $memberRole->id,
                'first_name' => 'Socio5',
                'last_name' => 'Test',
                'password' => Hash::make('socio'),
                'phone_number' => '55512341',
                'is_active' => true,
            ]
        );

        $member = Member::firstOrCreate(
            ['user_id' => $user->id],
            ['birth_date' => '1999-02-15']
        );

        MemberMembership::firstOrCreate(
            ['member_id' => $member->user_id, 'plan_id' => $elitePlan->id],
            [
                'status' => MembershipStatus::Active,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]
        );
    }
}
