<?php

namespace App\Models\AppointmentModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'rate_per_day',
        'employment_start',
        'employment_end',
        'source_of_fund',
        'location',
        'office_assignment',
        'appointment_type',
        'item_no',
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'gender',
        'birthday',
        'age',
        'updated_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'employment_start' => 'date',
        'employment_end' => 'date',
        'birthday' => 'date',
    ];
    
    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $middleInitial = $this->middle_name ? substr($this->middle_name, 0, 1) . '. ' : '';
        $extension = $this->extension_name ? ' ' . $this->extension_name : '';
        return $this->first_name . ' ' . $middleInitial . $this->last_name . $extension;
    }
    
    /**
     * Get the admin who updated this appointment
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
