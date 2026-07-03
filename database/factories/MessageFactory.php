<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
use App\Models\Company;
use App\Models\Conversation;
use App\Models\Contact;
use App\Models\ChannelConnection;

class MessageFactory extends Factory
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
            'conversation_id' => Conversation::factory(),
            'contact_id' => Contact::factory(),
            'channel_connection_id' => ChannelConnection::factory(),
            'sender_type' => 'contact',
            'sender_user_id' => null,
            'ai_agent_id' => null,
            'external_message_id' => 'wamid.' . $this->faker->regexify('[A-Za-z0-9+/=]{40}'),
            'direction' => 'inbound',
            'type' => 'text',
            'body' => $this->faker->sentence(),
            'status' => 'sent',
            'sent_at' => now(),
            'delivered_at' => null,
            'read_at' => null,
            'metadata' => null,
        ];
    }
}
