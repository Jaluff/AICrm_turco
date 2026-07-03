<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Department;
use App\Models\BusinessHour;
use Carbon\Carbon;

class BusinessHoursService
{
    /**
     * Determina si la empresa está abierta en la hora dada (o la hora actual).
     */
    public function isOpenForCompany(Company $company, ?Carbon $time = null): bool
    {
        if (!$company->business_hours_enabled) {
            return true;
        }

        $tz = $company->timezone ?? config('app.timezone');
        $time = ($time ? $time->copy() : now())->setTimezone($tz);
        $dayOfWeek = $time->dayOfWeekIso; // 1 = Lunes, ..., 7 = Domingo

        $businessHour = BusinessHour::where('company_id', $company->id)
            ->whereNull('department_id')
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$businessHour || !$businessHour->enabled) {
            return false;
        }

        $currentTime = $time->format('H:i:s');
        
        return $currentTime >= $businessHour->start_time && $currentTime <= $businessHour->end_time;
    }

    /**
     * Determina si el departamento está abierto en la hora dada (o la hora actual).
     */
    public function isOpenForDepartment(Department $department, ?Carbon $time = null): bool
    {
        if (!$department->business_hours_enabled) {
            return true;
        }

        if ($department->use_company_business_hours) {
            // Cargar relación company si no está presente
            $company = $department->company ?? Company::find($department->company_id);
            if (!$company) {
                return true;
            }
            return $this->isOpenForCompany($company, $time);
        }

        $tz = $department->company->timezone ?? config('app.timezone');
        $time = ($time ? $time->copy() : now())->setTimezone($tz);
        $dayOfWeek = $time->dayOfWeekIso;

        $businessHour = BusinessHour::where('company_id', $department->company_id)
            ->where('department_id', $department->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$businessHour || !$businessHour->enabled) {
            return false;
        }

        $currentTime = $time->format('H:i:s');
        
        return $currentTime >= $businessHour->start_time && $currentTime <= $businessHour->end_time;
    }
}
