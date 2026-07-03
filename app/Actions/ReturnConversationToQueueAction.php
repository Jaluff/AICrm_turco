<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Events\ConversationUpdated;

class ReturnConversationToQueueAction
{
    public function execute(Conversation $conversation, int $userId): Conversation
    {
        $oldUserId = $conversation->assigned_user_id;

        $conversation->update([
            'assigned_user_id' => null,
            'status' => 'open',
            'handler_type' => 'none',
            'handler_id' => null,
        ]);

        ConversationEvent::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'event_type' => 'returned_to_queue',
            'event_data' => [
                'old_assigned_user_id' => $oldUserId,
            ],
        ]);

        event(new ConversationUpdated($conversation));

        return $conversation;
    }
}
