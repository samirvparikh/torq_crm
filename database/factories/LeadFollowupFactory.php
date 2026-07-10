<?php

namespace Database\Factories;

use App\Enums\FollowupStatus;
use App\Enums\FollowupType;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadFollowup>
 */
class LeadFollowupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'type' => fake()->randomElement(FollowupType::values()),
            'status' => FollowupStatus::Pending->value,
            'subject' => fake()->sentence(4),
            'notes' => fake()->optional()->paragraph(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 week'),
            'completed_at' => null,
            'next_followup_at' => fake()->optional()->dateTimeBetween('+1 day', '+2 weeks'),
            'outcome' => null,
            'duration_minutes' => null,
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => FollowupStatus::Completed->value,
            'completed_at' => now(),
            'duration_minutes' => fake()->numberBetween(5, 60),
            'outcome' => fake()->sentence(),
        ]);
    }
}
