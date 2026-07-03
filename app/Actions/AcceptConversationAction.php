<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Events\ConversationUpdated;

class AcceptConversationAction
{
    public function execute(Conversation $conversation, int $userId): Conversation
    {
        $oldUserId = $conversation->assigned_user_id;

        $conversation->update([
            'assigned_user_id' => $userId,
            'status' => 'open',
            'handler_type' => 'human',
            'handler_id' => $userId,
            'assigned_at' => now(),
        ]);

        ConversationEvent::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'event_type' => 'assigned',
            'event_data' => [
                'old_assigned_user_id' => $oldUserId,
                'new_assigned_user_id' => $userId,
            ],
        ]);

        event(new ConversationUpdated($conversation));

        return $conversation;
    }
}
