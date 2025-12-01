<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemDocument extends Model
{
    use HasFactory;

    protected $table = 'system_documents';

    protected $fillable = [
        'name',
        'path',
        'type',
        'active',
        'virtual',
        'text',
    ];

    protected $casts = [
        'active' => 'boolean',
        'virtual' => 'boolean',
    ];
}
