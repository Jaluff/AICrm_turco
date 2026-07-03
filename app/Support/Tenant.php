<?php

namespace App\Support;

use App\Models\Company;

class Tenant
{
    protected static ?Company $tenant = null;

    public static function set(Company $company): void
    {
        self::$tenant = $company;
    }

    public static function get(): ?Company
    {
        return self::$tenant;
    }

    public static function id(): ?int
    {
        return self::$tenant?->id;
    }

    public static function clear(): void
    {
        self::$tenant = null;
    }
}
