<?php

namespace App\Http\Controllers\ServiceRecordController;

use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use App\Models\AppointmentModel\JobList;
use App\Models\ServiceRecordModel\ServiceRecord;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    /**
     * Display the service records page with employees and related data
     */
    public function index(Request $request)
    {
        // Get all appointments
        $appointments = Appointment::all();

        // Map each appointment to include the latest update time (from either appointment or service record)
        $employees = $appointments->map(function($employee) {
            $latestServiceRecord = ServiceRecord::where('employee_id', $employee->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            $serviceRecordTime = $latestServiceRecord
                ? ($latestServiceRecord->updated_at ?? $latestServiceRecord->created_at)
                : null;

            $appointmentTime = $employee->updated_at ?? $employee->created_at;

            $employee->latest_update = $serviceRecordTime && $serviceRecordTime > $appointmentTime
                ? $serviceRecordTime
                : $appointmentTime;

            return $employee;
        });

        // Sort by the most recent update time
        $employees = $employees->sortByDesc('latest_update')->values();

        // Paginate the results
        $currentPage = $request->input('page', 1);
        $perPage = 10;
        $total = $employees->count();
        $items = $employees->forPage($currentPage, $perPage);
        $paginatedEmployees = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jobLists = JobList::all();

        return view('Pages.Service_records.service_records', compact(
            'paginatedEmployees',
            'jobLists'
        ))->with('employees', $paginatedEmployees);
    }

    /**
     * Display employee record details
     */
    public function showEmployeeRecord($id)
    {
        // Get the appointment ID from the query string (if present)
        $selectedAppointmentId = request()->query('appointment_id');

        // Always get the main employee record by ID
        $employee = \App\Models\AppointmentModel\Appointment::findOrFail($id);
        
        // Get all appointments for this employee (history)
        $appointmentHistory = \App\Models\AppointmentModel\Appointment::where('employee_id', $employee->employee_id)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Determine which appointment to display (default to latest or current)
        if ($selectedAppointmentId) {
            $selectedAppointment = $appointmentHistory->where('id', $selectedAppointmentId)->first();
        } else {
            $selectedAppointment = $appointmentHistory->first(); // Latest by updated_at desc
        }

        // Check if the selected appointment is the latest
        $isLatestAppointment = $selectedAppointment && $selectedAppointment->id == $appointmentHistory->first()->id;

        // Get service records for this appointment
        $serviceRecords = ServiceRecord::where('employee_id', $selectedAppointment ? $selectedAppointment->id : $id)
            ->orderBy('updated_at', 'asc')
            ->get();

        // Detect if any service record is job_order
        $hasJobOrder = $serviceRecords->contains(function ($record) {
            return in_array(strtolower($record->status), ['job_order', 'joborder', 'job order']);
        });

        $typeLabels = config('appointment_types');
        return view('Pages.Service_records.Employee_records', compact(
            'employee',
            'serviceRecords',
            'isLatestAppointment',
            'appointmentHistory',
            'selectedAppointment',
            'typeLabels',
            'hasJobOrder' // Pass to view
        ));
    }
}
