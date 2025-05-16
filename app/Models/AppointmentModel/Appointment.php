<?php

namespace App\Models\AppointmentModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\EncryptsAttributes;

class Appointment extends Model
{
    use HasFactory, EncryptsAttributes;
    
    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'rate_per_day',
        'employee_id',
        'item_no',
    ];

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
     * Explicitly set the rate_per_day attribute to ensure it's a string.
     * This helps prevent PostgreSQL from trying to cast it to numeric.
     *
     * @param mixed $value
     * @return void
     */
    public function setRatePerDayAttribute($value)
    {
        // Force the value to be a string to prevent numeric conversion in PostgreSQL
        $this->attributes['rate_per_day'] = (string)$value;
    }
    
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
