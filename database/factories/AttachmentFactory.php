<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    public function definition(): array
    {
        $fileName = fake()->word().'.pdf';

        return [
            'attachable_type' => Lead::class,
            'attachable_id' => Lead::factory(),
            'file_name' => $fileName,
            'file_path' => 'attachments/'.fake()->uuid().'.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1024, 1048576),
            'collection' => 'default',
            'uploaded_by' => User::factory(),
        ];
    }
}
