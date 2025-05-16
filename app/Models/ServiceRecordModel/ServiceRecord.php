<?php

namespace App\Models\ServiceRecordModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppointmentModel\Appointment;
use App\Models\User;
use App\Traits\EncryptsAttributes;

class ServiceRecord extends Model
{
    use HasFactory, EncryptsAttributes;

    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'salary',
    ];

    protected $fillable = [
        'item_no',
        'employee_id',
        'date_from',
        'date_to',
        'designation',
        'status',   
        'salary',
        'payment_frequency',
        'station',
        'separation_date',
        'service_status',
        'is_permanent',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'is_permanent' => 'boolean',
        'employee_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];
    
    /**
     * Explicitly set the salary attribute to ensure it's a string.
     * This helps prevent PostgreSQL from trying to cast it to numeric.
     *
     * @param mixed $value
     * @return void
     */
    public function setSalaryAttribute($value)
    {
        // Force the value to be a string to prevent numeric conversion in PostgreSQL
        $this->attributes['salary'] = (string)$value;
    }

    /**
     * Get the employee that owns the service record.
     */
    public function employee()
    {
        return $this->belongsTo(Appointment::class, 'employee_id');
    }

    /**
     * Get the user who created the record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user who last updated the record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}