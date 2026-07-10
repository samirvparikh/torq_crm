<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadProduct>
 */
class LeadProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'product_id' => Product::factory(),
            'product_name' => fake()->words(3, true),
            'quantity' => fake()->numberBetween(1, 50).' Pcs',
            'unit_price' => fake()->randomFloat(2, 100, 10000),
            'tax_rate' => fake()->randomElement([0, 5, 12, 18]),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
