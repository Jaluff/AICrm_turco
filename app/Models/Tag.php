<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'color',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class);
    }
}
