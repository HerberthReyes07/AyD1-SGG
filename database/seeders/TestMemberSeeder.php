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
        $plan = MembershipPlan::where('name', 'Elite - Monthly')->firstOrFail();

        $user = User::firstOrCreate(
            ['email' => 'socio7@test.com'],
            [
                'role_id' => $memberRole->id,
                'first_name' => 'Socio7',
                'last_name' => 'Test',
                'password' => Hash::make('socio'),
                'phone_number' => '55510386',
                'is_active' => true,
            ]
        );

        $member = Member::firstOrCreate(
            ['user_id' => $user->id],
            ['birth_date' => '1999-02-17']
        );

        MemberMembership::firstOrCreate(
            ['member_id' => $member->user_id, 'plan_id' => $plan->id],
            [
                'status' => MembershipStatus::Active,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]
        );
    }
}
