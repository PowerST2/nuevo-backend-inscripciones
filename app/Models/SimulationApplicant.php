<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SimulationApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'last_name_father',
        'last_name_mother',
        'first_names',
        'email',
        'phone_mobile',
        'phone_other',
        'exam_simulation_id',
    ];


    public function examSimulation()
    {
        return $this->belongsTo(ExamSimulation::class);
    }



}
