<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationReasonAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationReasonAssignmentFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'conversation_id',
        'contact_reason_id',
        'assigned_user_id',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(ContactReason::class, 'contact_reason_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
