<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'description' => fake()->optional()->paragraph(),
            'unit' => fake()->randomElement(['Pcs', 'Kg', 'Box', 'Set']),
            'price' => fake()->randomFloat(2, 100, 50000),
            'tax_rate' => fake()->randomElement([0, 5, 12, 18, 28]),
            'hsn_code' => fake()->optional()->numerify('####'),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
