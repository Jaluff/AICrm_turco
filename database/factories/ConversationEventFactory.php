<?php

namespace Database\Factories;

use App\Models\ConversationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConversationEvent>
 */
use App\Models\Company;
use App\Models\Conversation;

class ConversationEventFactory extends Factory
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
            'user_id' => null,
            'event_type' => 'status_changed',
            'event_data' => ['old_status' => 'pending_human', 'new_status' => 'open'],
        ];
    }
}
