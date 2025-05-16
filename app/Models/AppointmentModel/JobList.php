<?php

namespace App\Models\AppointmentModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\EncryptsAttributes;

class JobList extends Model
{
    use HasFactory, EncryptsAttributes;
    
    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'title',
        'description',
        'salary_grade'
    ];

    protected $fillable = [
        'title',
        'status',
        'description',
        'salary_grade',
        'position_type',
    ];
    
    /**
     * Get formatted salary grade for display
     *
     * @return string
     */
    public function getFormattedSalaryAttribute()
    {
        return 'SG ' . $this->salary_grade;
    }
}