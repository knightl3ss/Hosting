<?php

namespace App\Models\AppointmentModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobList extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'description',
        'salary_grade',
        'position_type',
    ];
} 