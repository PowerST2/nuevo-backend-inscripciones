<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * $table->string('code')->unique();
            $table->string('name');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('period_id')->constrained('periods')->onDelete('cascade');

 *
 * One to One / Has One
 *  */
class ScheduleActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'start_time',
        'end_time',
        'period_id',
    ];
}
