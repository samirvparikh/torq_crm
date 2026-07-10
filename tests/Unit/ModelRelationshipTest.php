<?php

namespace Tests\Unit;

use App\Enums\LeadStatus;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerContactPerson;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\LeadSource;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            LeadSourceSeeder::class,
        ]);
    }

    public function test_lead_belongs_to_relationships(): void
    {
        $user = User::factory()->create();
        $source = LeadSource::query()->where('slug', 'indiamart')->first();
        $category = Category::factory()->create();
        $company = Company::factory()->create(['created_by' => $user->id]);
        $customer = Customer::factory()->create([
            'company_id' => $company->id,
            'created_by' => $user->id,
        ]);

        $lead = Lead::factory()->create([
            'lead_source_id' => $source->id,
            'category_id' => $category->id,
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->assertInstanceOf(LeadSource::class, $lead->leadSource);
        $this->assertInstanceOf(Category::class, $lead->category);
        $this->assertInstanceOf(Company::class, $lead->company);
        $this->assertInstanceOf(Customer::class, $lead->customer);
        $this->assertInstanceOf(User::class, $lead->assignee);
        $this->assertInstanceOf(User::class, $lead->creator);
        $this->assertEquals(LeadStatus::New, $lead->status);
    }

    public function test_lead_has_many_relationships(): void
    {
        $lead = Lead::factory()->create();

        LeadFollowup::factory()->count(2)->create(['lead_id' => $lead->id]);
        Task::factory()->count(2)->create(['lead_id' => $lead->id]);
        Quotation::factory()->create(['lead_id' => $lead->id]);

        $lead->refresh();

        $this->assertCount(2, $lead->followups);
        $this->assertCount(2, $lead->tasks);
        $this->assertCount(1, $lead->quotations);
    }

    public function test_customer_has_contact_persons_and_addresses(): void
    {
        $customer = Customer::factory()->create();

        CustomerContactPerson::factory()->count(2)->create(['customer_id' => $customer->id]);
        CustomerAddress::factory()->count(2)->create(['customer_id' => $customer->id]);

        $customer->refresh();

        $this->assertCount(2, $customer->contactPersons);
        $this->assertCount(2, $customer->addresses);
    }

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($product->category->is($category));
        $this->assertCount(1, $category->products);
    }

    public function test_user_has_assigned_leads(): void
    {
        $user = User::factory()->create();
        Lead::factory()->count(3)->create(['assigned_to' => $user->id]);

        $this->assertCount(3, $user->assignedLeads);
    }

    public function test_lead_factory_indiamart_state(): void
    {
        $lead = Lead::factory()->indiamart()->create();

        $this->assertNotNull($lead->indiamart_lead_id);
        $this->assertIsArray($lead->raw_data);
        $this->assertEquals('indiamart', $lead->leadSource->slug);
    }

    public function test_lead_active_scope_excludes_closed_statuses(): void
    {
        Lead::factory()->create(['status' => LeadStatus::New->value]);
        Lead::factory()->won()->create();
        Lead::factory()->create(['status' => LeadStatus::Lost->value]);

        $this->assertCount(1, Lead::query()->active()->get());
    }
}
