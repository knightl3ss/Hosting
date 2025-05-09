<?php

namespace App\Models\ServiceRecordModel;

use Illuminate\Database\Eloquent\Model;

class RecordPurpose extends Model
{
    protected $fillable = [
        'employee_id',
        'purpose_type',
        'purpose',
        'purpose_details',
        'requested_date',
        'status',
    ];

    protected $attributes = [
        'status' => 'Pending',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\AppointmentModel\Appointment::class, 'employee_id');
    }
}
