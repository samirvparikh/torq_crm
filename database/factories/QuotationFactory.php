<?php

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotation>
 */
class QuotationFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 1000, 100000);

        return [
            'quotation_number' => 'QT-'.fake()->unique()->numerify('######'),
            'lead_id' => Lead::factory(),
            'customer_id' => Customer::factory(),
            'company_id' => null,
            'quotation_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'status' => QuotationStatus::Draft->value,
            'subtotal' => $subtotal,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'tax_amount' => round($subtotal * 0.18, 2),
            'total' => round($subtotal * 1.18, 2),
            'terms' => fake()->optional()->paragraph(),
            'notes' => fake()->optional()->sentence(),
            'pdf_path' => null,
            'created_by' => User::factory(),
            'sent_at' => null,
        ];
    }
}
