<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Events\ConversationUpdated;

class ResolveConversationAction
{
    public function execute(Conversation $conversation, int $userId): Conversation
    {
        $oldStatus = $conversation->status;

        $conversation->update([
            'status' => 'closed',
            'resolved_at' => now(),
            'handler_type' => 'none',
            'handler_id' => null,
        ]);

        ConversationEvent::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'event_type' => 'status_changed',
            'event_data' => [
                'old_status' => $oldStatus,
                'new_status' => 'closed',
            ],
        ]);

        event(new ConversationUpdated($conversation));

        return $conversation;
    }
}
