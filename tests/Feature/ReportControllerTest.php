<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test dashboard loads for authenticated user
     */
    public function test_dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.dashboard');
    }

    /**
     * Test reports list displays
     */
    public function test_reports_list_loads()
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.reports-list');
    }

    /**
     * Test create report form
     */
    public function test_create_report_form_loads()
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.create');
    }

    /**
     * Test can create report with valid data
     */
    public function test_can_create_report_with_valid_data()
    {
        $data = [
            'report_type' => 'sales',
            'title' => 'Test Sales Report',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.reports.store'), $data);
        
        $response->assertRedirect(route('admin.reports.index'));
        $this->assertDatabaseHas('reports', [
            'title' => 'Test Sales Report',
            'report_type' => 'sales',
            'generated_by' => $this->user->id,
        ]);
    }

    /**
     * Test validation fails with missing required fields
     */
    public function test_validation_fails_without_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('admin.reports.store'), []);
        
        $response->assertSessionHasErrors(['report_type', 'title', 'start_date', 'end_date']);
    }

    /**
     * Test validation fails with invalid date range
     */
    public function test_validation_fails_with_invalid_date_range()
    {
        $data = [
            'report_type' => 'sales',
            'title' => 'Invalid Report',
            'start_date' => '2026-01-31',
            'end_date' => '2026-01-01', // End before start
        ];

        $response = $this->actingAs($this->user)->post(route('admin.reports.store'), $data);
        
        $response->assertSessionHasErrors('end_date');
    }

    /**
     * Test can view report detail
     */
    public function test_can_view_report_detail()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get(route('admin.reports.show', $report));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.show');
        $response->assertViewHas('report', $report);
    }

    /**
     * Test edit report form loads
     */
    public function test_edit_report_form_loads()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->get(route('admin.reports.edit', $report));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.edit');
        $response->assertViewHas('report', $report);
    }

    /**
     * Test can update report
     */
    public function test_can_update_report()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $data = [
            'title' => 'Updated Title',
            'report_type' => 'production',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-28',
        ];

        $response = $this->actingAs($this->user)->patch(route('admin.reports.update', $report), $data);
        
        $response->assertRedirect(route('admin.reports.show', $report));
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'title' => 'Updated Title',
            'report_type' => 'production',
        ]);
    }

    /**
     * Test can delete report
     */
    public function test_can_delete_report()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)->delete(route('admin.reports.destroy', $report));
        
        $response->assertRedirect(route('admin.reports.index'));
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    /**
     * Test can export report as CSV
     */
    public function test_can_export_report_as_csv()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('admin.reports.export', ['report' => $report, 'format' => 'csv']));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');
    }

    /**
     * Test can export report as PDF
     */
    public function test_can_export_report_as_pdf()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('admin.reports.export', ['report' => $report, 'format' => 'pdf']));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test dashboard filters work
     */
    public function test_dashboard_with_date_filters()
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.dashboard', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]));
        
        $response->assertStatus(200);
        $response->assertViewHas(['startDate', 'endDate']);
    }

    /**
     * Test reports list filters by type
     */
    public function test_reports_list_filters_by_type()
    {
        Report::factory(3)->create(['report_type' => 'sales']);
        Report::factory(2)->create(['report_type' => 'production']);
        
        $response = $this->actingAs($this->user)->get(route('admin.reports.index', ['type' => 'sales']));
        
        $response->assertStatus(200);
    }

    /**
     * Test reports list filters by status
     */
    public function test_reports_list_filters_by_status()
    {
        Report::factory(3)->create(['status' => 'completed']);
        Report::factory(1)->create(['status' => 'failed']);
        
        $response = $this->actingAs($this->user)->get(route('admin.reports.index', ['status' => 'completed']));
        
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated user cannot access reports
     */
    public function test_unauthenticated_user_cannot_access_reports()
    {
        $response = $this->get(route('admin.reports.dashboard'));
        
        $response->assertStatus(302); // Redirect to login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test invalid export format returns error
     */
    public function test_invalid_export_format_returns_error()
    {
        $report = Report::factory()->create(['generated_by' => $this->user->id]);
        
        $response = $this->actingAs($this->user)
            ->get(route('admin.reports.export', ['report' => $report, 'format' => 'invalid']));
        
        $response->assertStatus(302); // Redirect with error
        $response->assertSessionHas('error');
    }
}
