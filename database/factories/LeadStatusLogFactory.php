<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadStatusLog>
 */
class LeadStatusLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'from_status' => LeadStatus::New->value,
            'to_status' => LeadStatus::Assigned->value,
            'notes' => fake()->optional()->sentence(),
            'changed_by' => User::factory(),
        ];
    }
}
