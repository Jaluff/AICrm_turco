<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMessage extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'contact_id',
        'conversation_id',
        'channel_connection_id',
        'message_template_id',
        'body',
        'variables',
        'send_at',
        'status',
        'sent_message_id',
        'error',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'send_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function channelConnection(): BelongsTo
    {
        return $this->belongsTo(ChannelConnection::class);
    }

    public function messageTemplate(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sentMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'sent_message_id');
    }
}
