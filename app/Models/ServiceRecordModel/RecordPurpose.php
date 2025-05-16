<?php

namespace App\Models\ServiceRecordModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordPurpose extends Model
{
    protected $fillable = [
        'item_no',
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

    public function employee(): BelongsTo
    {
        // If item_no is not null, use item_no to find the employee
        if (!empty($this->item_no)) {
            return $this->belongsTo(\App\Models\AppointmentModel\Appointment::class, 'item_no', 'item_no');
        }
        
        // Otherwise use employee_id (default)
        return $this->belongsTo(\App\Models\AppointmentModel\Appointment::class, 'employee_id');
    }
}
