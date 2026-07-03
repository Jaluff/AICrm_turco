<?php

namespace App\Support\Traits;

use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if ($model->company_id === null && Tenant::id() !== null) {
                $model->company_id = Tenant::id();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
