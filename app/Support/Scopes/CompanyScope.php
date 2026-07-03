<?php

namespace App\Support\Scopes;

use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Tenant::id() !== null) {
            $builder->where($model->getTable() . '.company_id', Tenant::id());
        }
    }
}
