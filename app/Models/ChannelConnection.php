<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;

class ChannelConnection extends Model
{
    /** @use HasFactory<\Database\Factories\ChannelConnectionFactory> */
    use HasFactory, BelongsToCompany;

    const TYPE_WHATSAPP_CLOUD = 'whatsapp_cloud';
    const TYPE_WEBCHAT = 'webchat';
    const TYPE_INSTAGRAM = 'instagram';
    const TYPE_FACEBOOK = 'facebook';

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'status',
        'external_business_id',
        'external_phone_number_id',
        'external_waba_id',
        'phone_number',
        'access_token',
        'verify_token',
        'app_secret',
        'greeting_message',
        'farewell_message',
        'metadata',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'app_secret' => 'encrypted',
        'metadata' => 'array',
    ];
}
