<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Holiday>
 */
class HolidayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'date' => fake()->dateTimeBetween('now', '+1 year'),
            'is_recurring' => false,
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
