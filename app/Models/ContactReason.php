<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;

class ContactReason extends Model
{
    /** @use HasFactory<\Database\Factories\ContactReasonFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
