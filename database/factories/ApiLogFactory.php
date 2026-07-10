<?php

namespace Database\Factories;

use App\Enums\ApiLogStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiLog>
 */
class ApiLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'provider' => 'indiamart',
            'endpoint' => '/wservce/crm/crmListing/v2/',
            'method' => 'GET',
            'lead_id' => null,
            'request_headers' => ['Accept' => 'application/json'],
            'request_body' => null,
            'response_body' => json_encode(['CODE' => 200, 'MESSAGE' => 'Success']),
            'response_code' => 200,
            'response_time_ms' => fake()->numberBetween(100, 3000),
            'status' => ApiLogStatus::Success->value,
            'retry_count' => 0,
            'error_message' => null,
        ];
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => ApiLogStatus::Failed->value,
            'response_code' => 500,
            'error_message' => 'API request failed',
        ]);
    }
}
