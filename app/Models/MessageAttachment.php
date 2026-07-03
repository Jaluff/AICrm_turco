<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    /** @use HasFactory<\Database\Factories\MessageAttachmentFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'message_id',
        'type',
        'url',
        'filename',
        'mime_type',
        'size',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
