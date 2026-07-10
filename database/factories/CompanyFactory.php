<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'gst_number' => fake()->optional()->numerify('##AAAAA####A#Z#'),
            'pan' => fake()->optional()->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('9#########'),
            'alternate_phone' => null,
            'website' => fake()->optional()->url(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'India',
            'pincode' => fake()->postcode(),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
