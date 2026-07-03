<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'nickname',
        'phone',
        'email',
        'language',
        'avatar_url',
        'opt_in',
        'opt_out',
        'custom_fields',
    ];

    protected $casts = [
        'opt_in' => 'boolean',
        'opt_out' => 'boolean',
        'custom_fields' => 'array',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(ContactIdentity::class);
    }
}
