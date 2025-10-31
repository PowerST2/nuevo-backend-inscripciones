<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'name',
    ];

    public function scheduleActivities(): HasMany
    {
        return $this->hasMany(ScheduleActivity::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
