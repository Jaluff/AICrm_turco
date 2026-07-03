<?php

namespace Database\Factories;

use App\Models\ConversationReasonAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConversationReasonAssignment>
 */
use App\Models\Company;
use App\Models\Conversation;
use App\Models\ContactReason;

class ConversationReasonAssignmentFactory extends Factory
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
            'contact_reason_id' => ContactReason::factory(),
            'assigned_user_id' => null,
        ];
    }
}
