<?php

namespace Tests\Unit;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadService;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeadService $leadService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolePermissionSeeder::class, LeadSourceSeeder::class]);
        $this->leadService = app(LeadService::class);
    }

    public function test_it_creates_lead_with_number_and_activity(): void
    {
        $user = User::factory()->create();

        $lead = $this->leadService->create([
            'customer_name' => 'Test Customer',
            'mobile' => '9876543210',
        ], $user);

        $this->assertNotNull($lead->lead_number);
        $this->assertEquals('Test Customer', $lead->customer_name);
        $this->assertEquals(LeadStatus::New, $lead->status);
        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'type' => 'Lead Created',
        ]);
    }

    public function test_it_prevents_duplicate_indiamart_lead(): void
    {
        $user = User::factory()->create();

        Lead::factory()->create([
            'indiamart_lead_id' => 'IM123456',
            'customer_name' => 'Existing',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->leadService->create([
            'customer_name' => 'Duplicate',
            'indiamart_lead_id' => 'IM123456',
        ], $user);
    }

    public function test_it_changes_lead_status_and_logs(): void
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(['created_by' => $user->id]);

        $updated = $this->leadService->changeStatus($lead, LeadStatus::Won, $user);

        $this->assertEquals(LeadStatus::Won, $updated->status);
        $this->assertNotNull($updated->won_at);
        $this->assertDatabaseHas('lead_status_logs', [
            'lead_id' => $lead->id,
            'to_status' => LeadStatus::Won->value,
        ]);
    }
}
