<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $receptionist;
    private User $trainer;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrador']);
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist'], ['description' => 'Recepcionista']);
        $trainerRole = Role::firstOrCreate(['name' => 'trainer'], ['description' => 'Entrenador']);
        $memberRole = Role::firstOrCreate(['name' => 'member'], ['description' => 'Socio']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->receptionist = User::factory()->create(['role_id' => $receptionistRole->id]);
        $this->trainer = User::factory()->create(['role_id' => $trainerRole->id]);
        $this->member = User::factory()->create(['role_id' => $memberRole->id]);
    }

    public function test_non_admin_users_cannot_access_income_report(): void
    {
        $this->actingAs($this->receptionist)->get(route('reports.income.index'))->assertStatus(403);
        $this->actingAs($this->trainer)->get(route('reports.income.index'))->assertStatus(403);
        $this->actingAs($this->member)->get(route('reports.income.index'))->assertStatus(403);
    }

    public function test_non_admin_users_cannot_access_expiration_report(): void
    {
        $this->actingAs($this->receptionist)->get(route('reports.membership-expiration.index'))->assertStatus(403);
        $this->actingAs($this->trainer)->get(route('reports.membership-expiration.index'))->assertStatus(403);
        $this->actingAs($this->member)->get(route('reports.membership-expiration.index'))->assertStatus(403);
    }

    public function test_admin_can_view_income_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.income.index'));

        $response->assertStatus(200);
        $response->assertSee('Reporte de Ingresos');
    }

    public function test_admin_can_view_membership_expiration_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.membership-expiration.index'));

        $response->assertStatus(200);
        $response->assertSee('Reporte de Vencimiento de Membresías');
    }

    public function test_admin_can_export_income_report_excel_and_csv(): void
    {
        $excelResponse = $this->actingAs($this->admin)->get(route('reports.income.export-excel'));
        $excelResponse->assertStatus(200);

        $csvResponse = $this->actingAs($this->admin)->get(route('reports.income.export-csv'));
        $csvResponse->assertStatus(200);
    }

    public function test_admin_can_export_expiration_report_excel_and_csv(): void
    {
        $excelResponse = $this->actingAs($this->admin)->get(route('reports.membership-expiration.export-excel'));
        $excelResponse->assertStatus(200);

        $csvResponse = $this->actingAs($this->admin)->get(route('reports.membership-expiration.export-csv'));
        $csvResponse->assertStatus(200);
    }

    public function test_admin_can_export_pdf_reports(): void
    {
        $incomePdf = $this->actingAs($this->admin)->post(route('reports.income.export-pdf'), ['group_by' => 'month']);
        $incomePdf->assertStatus(200);

        $expPdf = $this->actingAs($this->admin)->post(route('reports.membership-expiration.export-pdf'));
        $expPdf->assertStatus(200);
    }
}
