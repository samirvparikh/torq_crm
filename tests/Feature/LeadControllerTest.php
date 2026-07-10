<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolePermissionSeeder::class, LeadSourceSeeder::class]);
    }

    public function test_super_admin_can_create_lead_via_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::SuperAdmin->value);

        $response = $this->actingAs($user)->postJson(route('leads.store'), [
            'customer_name' => 'John Doe',
            'mobile' => '9123456789',
            'email' => 'john@example.com',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('leads', [
            'customer_name' => 'John Doe',
            'mobile' => '9123456789',
        ]);
    }

    public function test_viewer_cannot_create_lead(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Viewer->value);

        $response = $this->actingAs($user)->postJson(route('leads.store'), [
            'customer_name' => 'John Doe',
        ]);

        $response->assertForbidden();
    }

    public function test_lead_datatable_returns_paginated_json(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Admin->value);

        Lead::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('leads.datatable'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data', 'meta' => ['total', 'current_page']]);
    }

    public function test_sales_executive_sees_only_assigned_leads_in_datatable(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::SalesExecutive->value);

        Lead::factory()->create(['assigned_to' => $user->id, 'customer_name' => 'Mine']);
        Lead::factory()->create(['customer_name' => 'Others']);

        $response = $this->actingAs($user)->getJson(route('leads.datatable'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Mine', $response->json('data.0.customer_name'));
    }
}
