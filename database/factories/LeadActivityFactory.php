<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadActivity>
 */
class LeadActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'type' => fake()->randomElement(ActivityType::values()),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'properties' => null,
            'causer_id' => User::factory(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
