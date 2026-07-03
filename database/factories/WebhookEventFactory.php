<?php

namespace Database\Factories;

use App\Models\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebhookEvent>
 */
use App\Models\Company;

class WebhookEventFactory extends Factory
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
            'channel_type' => 'whatsapp_cloud',
            'payload' => ['event' => 'test'],
            'status' => 'pending',
            'error_message' => null,
        ];
    }
}
