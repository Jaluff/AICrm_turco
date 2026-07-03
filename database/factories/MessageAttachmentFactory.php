<?php

namespace Database\Factories;

use App\Models\MessageAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageAttachment>
 */
use App\Models\Company;
use App\Models\Message;

class MessageAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'message_id' => Message::factory(),
            'type' => 'image',
            'url' => $this->faker->imageUrl(),
            'filename' => 'image.png',
            'mime_type' => 'image/png',
            'size' => 1024,
            'metadata' => null,
        ];
    }
}
