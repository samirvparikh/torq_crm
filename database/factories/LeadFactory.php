<?php

namespace Database\Factories;

use App\Enums\LeadPriority;
use App\Enums\LeadStatus;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_number' => 'LD-'.fake()->unique()->numerify('######'),
            'lead_source_id' => LeadSource::factory(),
            'indiamart_lead_id' => null,
            'customer_id' => null,
            'company_id' => null,
            'category_id' => Category::factory(),
            'customer_name' => fake()->name(),
            'company_name' => fake()->company(),
            'gst_number' => fake()->optional()->numerify('##AAAAA####A#Z#'),
            'mobile' => fake()->numerify('9#########'),
            'alternate_mobile' => null,
            'whatsapp' => fake()->optional()->numerify('9#########'),
            'email' => fake()->optional()->safeEmail(),
            'website' => fake()->optional()->url(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'India',
            'pincode' => fake()->postcode(),
            'interested_product' => fake()->words(3, true),
            'requirement' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(1, 100).' Pcs',
            'budget' => fake()->randomFloat(2, 5000, 500000),
            'priority' => fake()->randomElement(LeadPriority::values()),
            'status' => LeadStatus::New->value,
            'lost_reason' => null,
            'assigned_to' => null,
            'created_by' => User::factory(),
            'expected_closing_date' => fake()->optional()->dateTimeBetween('now', '+3 months'),
            'remarks' => fake()->optional()->sentence(),
            'last_contacted_at' => null,
            'next_followup_at' => fake()->optional()->dateTimeBetween('now', '+2 weeks'),
            'won_at' => null,
            'lost_at' => null,
            'won_value' => null,
            'raw_data' => null,
            'is_duplicate' => false,
            'duplicate_of_lead_id' => null,
        ];
    }

    public function indiamart(): static
    {
        return $this->state(fn () => [
            'indiamart_lead_id' => fake()->unique()->numerify('IM########'),
            'lead_source_id' => LeadSource::query()->where('slug', 'indiamart')->value('id')
                ?? LeadSource::factory()->create(['name' => 'IndiaMART', 'slug' => 'indiamart'])->id,
            'raw_data' => ['source' => 'indiamart', 'synced_at' => now()->toIso8601String()],
        ]);
    }

    public function assigned(?User $user = null): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::Assigned->value,
            'assigned_to' => $user?->id ?? User::factory(),
        ]);
    }

    public function won(): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::Won->value,
            'won_at' => now(),
            'won_value' => fake()->randomFloat(2, 10000, 500000),
        ]);
    }

    public function withCustomer(): static
    {
        return $this->state(fn () => [
            'customer_id' => Customer::factory(),
            'company_id' => Company::factory(),
        ]);
    }
}
