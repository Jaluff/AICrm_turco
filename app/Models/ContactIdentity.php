<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactIdentity extends Model
{
    /** @use HasFactory<\Database\Factories\ContactIdentityFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'contact_id',
        'channel_type',
        'external_id',
        'phone',
        'username',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
