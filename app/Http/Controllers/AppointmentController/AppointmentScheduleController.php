<?php

namespace App\Http\Controllers\AppointmentController;

use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use App\Models\AppointmentModel\JobList;
use App\Http\Controllers\Controller;

class AppointmentScheduleController extends Controller
{
    // Show Appointment Schedule Page
    public function showSchedule(Request $request)
    {
        $appointmentType = $request->input('appointment_type', null);
        $search = $request->input('search', null);
        
        $query = Appointment::query();
        
        if ($appointmentType) {
            $query->where('appointment_type', $appointmentType);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('middle_name', 'like', "%$search%")
                  ->orWhere('office_assignment', 'like', "%$search%")
                  ->orWhere('employee_id', 'like', "%$search%")
                  ->orWhere('item_no', 'like', "%$search%");
            });
        }

        // Get all appointments without grouping
        $appointments = $query->latest('updated_at')->get();
        
        $jobLists = JobList::all();
        $temporaryEmployees = Appointment::where('appointment_type', 'temporary')
            ->latest('updated_at')
            ->get();

        // PAGINATE APPOINTMENTS (10 per page)
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $typeLabels = config('appointment_types');
        
        $pagedAppointments = new \Illuminate\Pagination\LengthAwarePaginator(
            $appointments->forPage($currentPage, $perPage),
            $appointments->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('Pages.appointments.schedule', compact(
            'pagedAppointments', 
            'appointmentType', 
            'jobLists',
            'temporaryEmployees',
            'search',
            'typeLabels'
        ));
    }
}
