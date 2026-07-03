<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationEvent extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationEventFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'conversation_id',
        'user_id',
        'event_type',
        'event_data',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
