<?php

namespace App\Http\Controllers\ServiceRecordController;

use Illuminate\Support\Facades\DB;
use App\Models\AppointmentModel\Appointment;
use App\Http\Controllers\Controller;

class PrintController extends Controller
{
    /**
     * Display printable employee service record
     */
    public function printEmployeeRecords($id)
    {
        // Check for selected appointment_id in the query string
        $selectedAppointmentId = request()->query('appointment_id');
        if ($selectedAppointmentId) {
            $employee = Appointment::findOrFail($selectedAppointmentId);
            $serviceRecords = DB::table('service_records')
                ->where('employee_id', $selectedAppointmentId)
                ->orderBy('date_from', 'asc')
                ->get();
        } else {
            $employee = Appointment::findOrFail($id);
            $serviceRecords = DB::table('service_records')
                ->where('employee_id', $id)
                ->orderBy('date_from', 'asc')
                ->get();
        }
        $typeLabels = config('appointment_types');
        return view('Pages.Service_records.Print_Employee_Records', compact('employee', 'serviceRecords', 'typeLabels'));
    }
}
