<?php
namespace App\Traits;

use App\Models\Gender;

trait GenderTrait
{
    /**
     * Get all genders (only id and name)
     */
    public function getGenders()
    {
        return Gender::select('id', 'name')->get();
    }
}