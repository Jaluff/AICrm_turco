<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'greeting_message',
        'farewell_message',
        'away_message',
        'auto_assignment_enabled',
        'assign_offline_enabled',
        'redistribute_unavailable_enabled',
        'ai_enabled',
        'business_hours_enabled',
        'use_company_business_hours',
    ];

    protected $casts = [
        'auto_assignment_enabled' => 'boolean',
        'assign_offline_enabled' => 'boolean',
        'redistribute_unavailable_enabled' => 'boolean',
        'ai_enabled' => 'boolean',
        'business_hours_enabled' => 'boolean',
        'use_company_business_hours' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('receives_auto_assignment');
    }

    public function businessHours(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BusinessHour::class);
    }
}
