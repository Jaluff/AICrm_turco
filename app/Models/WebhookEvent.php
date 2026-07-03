<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;

class WebhookEvent extends Model
{
    /** @use HasFactory<\Database\Factories\WebhookEventFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'channel_type',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
