<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'default_language',
        'status',
        'business_hours_enabled',
        'away_message',
    ];

    protected $casts = [
        'business_hours_enabled' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function businessHours(): HasMany
    {
        return $this->hasMany(BusinessHour::class)->whereNull('department_id');
    }
}
