<?php

namespace App\Http\Controllers\AppointmentController;

use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use App\Http\Controllers\Controller;

class AppointmentShowController extends Controller
{
    // Show details of a specific appointment
    public function show($id) {
        $appointment = Appointment::findOrFail($id);
        $typeLabels = config('appointment_types');
        $appointmentTypeLabel = $typeLabels[$appointment->appointment_type] ?? $appointment->appointment_type;
        return view('Pages.appointments.show', [
            'appointment' => $appointment,
            'typeLabels' => $typeLabels,
            'appointmentTypeLabel' => $appointmentTypeLabel,
        ]);
    }
}
