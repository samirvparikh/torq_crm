<?php

namespace Database\Factories;

use App\Enums\LeadPriority;
use App\Enums\TaskStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'lead_id' => Lead::factory(),
            'customer_id' => null,
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'priority' => fake()->randomElement(LeadPriority::values()),
            'status' => TaskStatus::Pending->value,
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'reminder_at' => fake()->optional()->dateTimeBetween('now', '+1 week'),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Completed->value,
            'completed_at' => now(),
        ]);
    }
}
