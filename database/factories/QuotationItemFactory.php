<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuotationItem>
 */
class QuotationItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 100, 5000);
        $taxRate = fake()->randomElement([0, 5, 12, 18]);
        $lineTotal = $quantity * $unitPrice;
        $taxAmount = round($lineTotal * ($taxRate / 100), 2);

        return [
            'quotation_id' => Quotation::factory(),
            'product_id' => Product::factory(),
            'product_name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'unit' => 'Pcs',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount' => 0,
            'total' => $lineTotal + $taxAmount,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
