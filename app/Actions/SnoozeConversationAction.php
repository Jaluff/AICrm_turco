<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Events\ConversationUpdated;
use Carbon\Carbon;

class SnoozeConversationAction
{
    public function execute(Conversation $conversation, int $userId, Carbon $until): Conversation
    {
        $oldStatus = $conversation->status;

        $conversation->update([
            'status' => 'snoozed',
            'snoozed_until' => $until,
            'handler_type' => 'none',
            'handler_id' => null,
        ]);

        ConversationEvent::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'event_type' => 'snoozed',
            'event_data' => [
                'old_status' => $oldStatus,
                'new_status' => 'snoozed',
                'snoozed_until' => $until->toIso8601String(),
            ],
        ]);

        event(new ConversationUpdated($conversation));

        return $conversation;
    }
}
