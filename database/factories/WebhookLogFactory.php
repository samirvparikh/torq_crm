<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookLog>
 */
class WebhookLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'provider' => 'indiamart',
            'event_type' => 'lead.created',
            'lead_id' => null,
            'headers' => ['Content-Type' => 'application/json'],
            'payload' => json_encode(['event' => 'lead.created']),
            'status' => 'received',
            'processed_at' => null,
            'error_message' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
