<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'contact_id',
        'channel_connection_id',
        'department_id',
        'assigned_user_id',
        'status',
        'handler_type',
        'handler_id',
        'flags',
        'keep_assigned',
        'snoozed_until',
        'first_response_at',
        'assigned_at',
        'resolved_at',
        'last_message_at',
        'metadata',
    ];

    protected $casts = [
        'flags' => 'array',
        'keep_assigned' => 'boolean',
        'snoozed_until' => 'datetime',
        'first_response_at' => 'datetime',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function channelConnection(): BelongsTo
    {
        return $this->belongsTo(ChannelConnection::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ConversationEvent::class);
    }

    public function reasons(): BelongsToMany
    {
        return $this->belongsToMany(ContactReason::class, 'conversation_reason_assignments', 'conversation_id', 'contact_reason_id')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
