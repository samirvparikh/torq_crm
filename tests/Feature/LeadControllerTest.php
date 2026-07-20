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

    public function test_marketing_can_create_lead(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Marketing->value);

        $response = $this->actingAs($user)->postJson(route('leads.store'), [
            'customer_name' => 'John Doe',
            'mobile' => '9123456789',
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
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

    public function test_manager_sees_all_leads_in_datatable(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Manager->value);

        Lead::factory()->create(['assigned_to' => $user->id, 'customer_name' => 'Mine']);
        Lead::factory()->create(['customer_name' => 'Others']);

        $response = $this->actingAs($user)->getJson(route('leads.datatable'));

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
