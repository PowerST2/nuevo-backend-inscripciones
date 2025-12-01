<?php
namespace App\Traits;

use App\Models\Period;

trait PeriodTrait
{
    /**
     * Get all active periods
     */
    public function getPeriods()
    {
        return Period::where('active', true)->get();
    }

    /**
     * Get the current active period
     */
    public function getActivePeriod()
    {
        return Period::where('active', true)->first();
    }
}