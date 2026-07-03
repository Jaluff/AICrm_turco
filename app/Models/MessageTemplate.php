<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageTemplate extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'channel_connection_id',
        'external_template_id',
        'name',
        'language',
        'category',
        'status',
        'components',
        'variables',
    ];

    protected $casts = [
        'components' => 'array',
        'variables' => 'array',
    ];

    public function channelConnection(): BelongsTo
    {
        return $this->belongsTo(ChannelConnection::class);
    }
}
