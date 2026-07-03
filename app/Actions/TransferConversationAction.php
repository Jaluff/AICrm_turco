<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Events\ConversationUpdated;

class TransferConversationAction
{
    public function execute(
        Conversation $conversation,
        int $userId,
        ?int $targetUserId = null,
        ?int $targetDepartmentId = null,
        ?string $reason = null
    ): Conversation {
        $oldUserId = $conversation->assigned_user_id;
        $oldDepartmentId = $conversation->department_id;

        $updates = [];
        if ($targetUserId !== null) {
            $updates['assigned_user_id'] = $targetUserId;
            $updates['handler_type'] = 'human';
            $updates['handler_id'] = $targetUserId;
            $updates['assigned_at'] = now();
        }
        if ($targetDepartmentId !== null) {
            $updates['department_id'] = $targetDepartmentId;
            // Si transferimos a un departamento y no asignamos usuario, desasignamos el usuario anterior
            if ($targetUserId === null) {
                $updates['assigned_user_id'] = null;
                $updates['handler_type'] = 'none';
                $updates['handler_id'] = null;
            }
        }

        $conversation->update($updates);

        ConversationEvent::create([
            'company_id' => $conversation->company_id,
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'event_type' => 'transferred',
            'event_data' => [
                'old_assigned_user_id' => $oldUserId,
                'new_assigned_user_id' => $conversation->assigned_user_id,
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $conversation->department_id,
                'reason' => $reason,
            ],
        ]);

        event(new ConversationUpdated($conversation));

        return $conversation;
    }
}
