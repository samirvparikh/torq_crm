<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadAssignment>
 */
class LeadAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'assigned_at' => now(),
            'notes' => fake()->optional()->sentence(),
            'is_current' => true,
        ];
    }
}
