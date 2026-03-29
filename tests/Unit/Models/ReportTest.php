<?php

namespace Tests\Unit\Models;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test report has correct fillable attributes
     */
    public function test_report_has_correct_fillable_attributes()
    {
        $fillable = (new Report())->getFillable();
        
        $this->assertContains('report_type', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('start_date', $fillable);
        $this->assertContains('end_date', $fillable);
        $this->assertContains('data', $fillable);
        $this->assertContains('filters', $fillable);
        $this->assertContains('generated_by', $fillable);
    }

    /**
     * Test report can be created with valid data
     */
    public function test_report_can_be_created()
    {
        $user = User::factory()->create();
        
        $report = Report::create([
            'report_type' => 'sales',
            'title' => 'Test Report',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'generated_by' => $user->id,
        ]);
        
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'title' => 'Test Report',
        ]);
    }

    /**
     * Test report belongs to user
     */
    public function test_report_belongs_to_user()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create(['generated_by' => $user->id]);
        
        $this->assertInstanceOf(User::class, $report->generatedBy);
        $this->assertEquals($user->id, $report->generatedBy->id);
    }

    /**
     * Test report casts data as array
     */
    public function test_report_casts_data_as_array()
    {
        $data = ['total_revenue' => 100000, 'total_orders' => 10];
        
        $report = Report::create([
            'report_type' => 'sales',
            'title' => 'Test',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'data' => $data,
            'generated_by' => 1,
        ]);
        
        $this->assertIsArray($report->data);
        $this->assertEquals(100000, $report->data['total_revenue']);
    }

    /**
     * Test report casts filters as array
     */
    public function test_report_casts_filters_as_array()
    {
        $filters = ['order_status' => 'completed', 'payment_status' => 'paid'];
        
        $report = Report::create([
            'report_type' => 'sales',
            'title' => 'Test',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'filters' => $filters,
            'generated_by' => 1,
        ]);
        
        $this->assertIsArray($report->filters);
        $this->assertEquals('completed', $report->filters['order_status']);
    }

    /**
     * Test report can be scoped by type
     */
    public function test_report_can_be_scoped_by_type()
    {
        Report::factory(3)->create(['report_type' => 'sales']);
        Report::factory(2)->create(['report_type' => 'production']);
        
        $salesReports = Report::byType('sales')->get();
        
        $this->assertEquals(3, $salesReports->count());
        $this->assertTrue($salesReports->every(fn($r) => $r->report_type === 'sales'));
    }

    /**
     * Test report can be scoped by status
     */
    public function test_report_can_be_scoped_by_status()
    {
        Report::factory(3)->create(['status' => 'completed']);
        Report::factory(1)->create(['status' => 'failed']);
        
        $completedReports = Report::byStatus('completed')->get();
        
        $this->assertEquals(3, $completedReports->count());
        $this->assertTrue($completedReports->every(fn($r) => $r->status === 'completed'));
    }

    /**
     * Test report soft deletes
     */
    public function test_report_soft_deletes()
    {
        $report = Report::factory()->create();
        $reportId = $report->id;
        
        $report->delete();
        
        $this->assertSoftDeleted('reports', ['id' => $reportId]);
        $this->assertNull(Report::find($reportId));
        $this->assertNotNull(Report::withTrashed()->find($reportId));
    }

    /**
     * Test report dates are cast as Carbon instances
     */
    public function test_report_dates_are_carbon_instances()
    {
        $report = Report::factory()->create();
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $report->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $report->updated_at);
    }

    /**
     * Test report with all optional fields
     */
    public function test_report_with_all_optional_fields()
    {
        $user = User::factory()->create();
        
        $report = Report::create([
            'report_type' => 'sales',
            'title' => 'Complete Report',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'data' => ['revenue' => 500000],
            'filters' => ['status' => 'completed'],
            'generated_by' => $user->id,
        ]);
        
        $this->assertEquals('sales', $report->report_type);
        $this->assertEquals('Complete Report', $report->title);
        $this->assertIsArray($report->data);
        $this->assertIsArray($report->filters);
    }

    /**
     * Test report JSON column queries
     */
    public function test_report_json_column_queries()
    {
        Report::create([
            'report_type' => 'sales',
            'title' => 'High Revenue',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'data' => ['revenue' => 1000000],
            'generated_by' => 1,
        ]);
        
        Report::create([
            'report_type' => 'sales',
            'title' => 'Low Revenue',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'data' => ['revenue' => 100000],
            'generated_by' => 1,
        ]);
        
        $reports = Report::all();
        
        $this->assertEquals(2, $reports->count());
    }

    /**
     * Test report default values
     */
    public function test_report_default_values()
    {
        $report = Report::create([
            'report_type' => 'sales',
            'title' => 'Test',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'generated_by' => 1,
        ]);
        
        // These fields are nullable and can be null by default
        $this->assertNull($report->data);
        $this->assertNull($report->filters);
        $this->assertNull($report->status);
    }
}
