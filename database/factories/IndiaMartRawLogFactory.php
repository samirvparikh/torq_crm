<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndiaMartRawLog>
 */
class IndiaMartRawLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'request_json' => json_encode(['glusr_id' => fake()->numerify('########')]),
            'response_json' => json_encode(['UNIQUE_QUERY_ID' => fake()->numerify('########')]),
            'status' => 'success',
        ];
    }
}
