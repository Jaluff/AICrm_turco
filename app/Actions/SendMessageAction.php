<?php

namespace App\Actions;

use App\Models\Conversation;
use App\Models\Message;
use App\Jobs\SendOutgoingMessageJob;

class SendMessageAction
{
    /**
     * Registra un mensaje saliente en la base de datos y despacha el Job de envío.
     *
     * @param Conversation $conversation
     * @param string $body
     * @param string $senderType (human, ai, system)
     * @param int|null $senderUserId
     * @param string $type
     * @param array $metadata
     * @return Message
     */
    public function execute(
        Conversation $conversation,
        string $body,
        string $senderType = 'human',
        ?int $senderUserId = null,
        string $type = 'text',
        array $metadata = []
    ): Message {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'contact_id' => $conversation->contact_id,
            'channel_connection_id' => $conversation->channel_connection_id,
            'sender_type' => $senderType,
            'sender_user_id' => $senderUserId,
            'direction' => 'outbound',
            'type' => $type,
            'body' => $body,
            'status' => 'pending',
            'metadata' => $metadata,
        ]);

        SendOutgoingMessageJob::dispatch($message);

        return $message;
    }
}
