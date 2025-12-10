<?php
namespace App\Traits;

use App\Models\Period;

trait PeriodTrait
{
    /**
     * Get all active periods (only id and name)
     */
    public function getPeriods()
    {
        return Period::where('active', true)->select('id', 'name')->get();
    }

    /**
     * Get the current active period
     */
    public function getActivePeriod()
    {
        return Period::where('active', true)->select('id', 'name')->first();
    }
}