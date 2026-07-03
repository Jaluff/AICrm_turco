<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessHour extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'department_id',
        'day_of_week',
        'enabled',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
