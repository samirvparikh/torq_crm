<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => fake()->numerify('9#########'),
            'alternate_mobile' => null,
            'whatsapp' => fake()->optional()->numerify('9#########'),
            'gst_number' => fake()->optional()->numerify('##AAAAA####A#Z#'),
            'pan' => fake()->optional()->regexify('[A-Z]{5}[0-9]{4}[A-Z]{1}'),
            'website' => fake()->optional()->url(),
            'designation' => fake()->optional()->jobTitle(),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
