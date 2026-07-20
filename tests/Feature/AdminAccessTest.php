<?php

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_access_admin_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::SuperAdmin->value);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('permissions.index'))
            ->assertOk();
    }

    public function test_admin_can_access_admin_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Admin->value);

        $this->actingAs($user)->get(route('users.index'))->assertOk();
        $this->actingAs($user)->get(route('roles.index'))->assertOk();
        $this->actingAs($user)->get(route('permissions.index'))->assertOk();
    }

    public function test_manager_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Manager->value);

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('roles.index'))->assertForbidden();
        $this->actingAs($user)->get(route('permissions.index'))->assertForbidden();
    }

    public function test_marketing_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::Marketing->value);

        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('roles.index'))->assertForbidden();
        $this->actingAs($user)->get(route('permissions.index'))->assertForbidden();
    }

    public function test_roles_datatable_loads(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleName::SuperAdmin->value);

        $this->actingAs($user)
            ->getJson(route('roles.datatable'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data', 'meta']);

        $this->assertTrue(Role::query()->exists());
        $this->assertEqualsCanonicalizing(RoleName::values(), Role::query()->pluck('name')->all());
    }
}
