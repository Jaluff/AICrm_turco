<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
use App\Models\Company;
use App\Models\Contact;
use App\Models\ChannelConnection;

class ConversationFactory extends Factory
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
            'contact_id' => Contact::factory(),
            'channel_connection_id' => ChannelConnection::factory(),
            'department_id' => null,
            'assigned_user_id' => null,
            'status' => 'open',
            'handler_type' => 'none',
            'handler_id' => null,
            'flags' => null,
            'keep_assigned' => false,
            'snoozed_until' => null,
            'first_response_at' => null,
            'assigned_at' => null,
            'resolved_at' => null,
            'last_message_at' => now(),
            'metadata' => null,
        ];
    }
}
