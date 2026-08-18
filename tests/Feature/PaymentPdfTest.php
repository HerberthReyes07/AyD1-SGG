<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\MemberMembership;
use App\Models\MembershipPayment;
use App\Models\PaymentMethod;
use App\Enums\MembershipStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;
    protected User $memberUser1;
    protected User $memberUser2;
    protected MemberMembership $membership1;
    protected MemberMembership $membership2;
    protected MembershipPayment $payment1;
    protected MembershipPayment $payment2;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        $adminRole = Role::create(['name' => 'admin']);
        $receptionistRole = Role::create(['name' => 'receptionist']);
        $memberRole = Role::create(['name' => 'member']);

        // Users
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->receptionist = User::factory()->create(['role_id' => $receptionistRole->id]);

        $this->memberUser1 = User::factory()->create(['role_id' => $memberRole->id]);
        $member1 = Member::create([
            'user_id' => $this->memberUser1->id,
            'birth_date' => '1995-05-15',
        ]);

        $this->memberUser2 = User::factory()->create(['role_id' => $memberRole->id]);
        $member2 = Member::create([
            'user_id' => $this->memberUser2->id,
            'birth_date' => '1996-08-20',
        ]);

        // Plan & Method
        $plan = MembershipPlan::create([
            'name' => 'Basic - Monthly',
            'description' => 'Plan Básico',
            'price' => 150.00,
            'duration_months' => 1,
        ]);

        $method = PaymentMethod::create([
            'name' => 'Efectivo',
            'description' => 'Pago en efectivo',
        ]);

        // Memberships
        $this->membership1 = MemberMembership::create([
            'member_id' => $member1->user_id,
            'plan_id' => $plan->id,
            'status' => MembershipStatus::Active,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $this->membership2 = MemberMembership::create([
            'member_id' => $member2->user_id,
            'plan_id' => $plan->id,
            'status' => MembershipStatus::Active,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        // Payments
        $this->payment1 = MembershipPayment::create([
            'amount' => 150.00,
            'payment_date' => now(),
            'member_membership_id' => $this->membership1->id,
            'payment_method_id' => $method->id,
            'registered_by' => $this->admin->id,
        ]);

        $this->payment2 = MembershipPayment::create([
            'amount' => 150.00,
            'payment_date' => now(),
            'member_membership_id' => $this->membership2->id,
            'payment_method_id' => $method->id,
            'registered_by' => $this->admin->id,
        ]);
    }

    public function test_admin_and_receptionist_can_view_all_payments_and_download_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('payments.index'));
        $response->assertStatus(200);
        $response->assertSee('#' . $this->payment1->id);
        $response->assertSee('#' . $this->payment2->id);

        $responsePdf = $this->actingAs($this->receptionist)->get(route('payments.pdf', $this->payment1->id));
        $responsePdf->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePdf->headers->get('content-type'));
    }

    public function test_member_can_only_view_own_payments_and_download_own_pdf()
    {
        $response = $this->actingAs($this->memberUser1)->get(route('payments.index'));
        $response->assertStatus(200);
        $response->assertSee('#' . $this->payment1->id);
        $response->assertDontSee('#' . $this->payment2->id);

        $responsePdf = $this->actingAs($this->memberUser1)->get(route('payments.pdf', $this->payment1->id));
        $responsePdf->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePdf->headers->get('content-type'));
    }

    public function test_member_cannot_access_another_members_payment_or_pdf()
    {
        // Member 1 attempts to view Member 2's payment details
        $responseShow = $this->actingAs($this->memberUser1)->get(route('payments.show', $this->payment2->id));
        $responseShow->assertStatus(403);

        // Member 1 attempts to download Member 2's PDF
        $responsePdf = $this->actingAs($this->memberUser1)->get(route('payments.pdf', $this->payment2->id));
        $responsePdf->assertStatus(403);
    }
}
