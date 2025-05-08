<?php

namespace App\Http\Controllers\ServiceRecordController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use Illuminate\Support\Facades\DB;

class ServiceRecordFilterController extends Controller
{
    /**
     * Filter and return service records based on appointment type.
     */
    public function filter(Request $request)
    {
        $filterType = $request->input('appointment_type');
        $search = $request->input('search');
        
        // Start with a base query for appointments
        $query = Appointment::query();
        
        // Apply appointment type filter if specified
        if ($filterType) {
            $query->where('appointment_type', $filterType);
        }
        
        // Apply search filter if specified
        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(office_assignment) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(item_no) LIKE ?', ['%' . $searchLower . '%']);
            });
        }
        
        // Get all appointments and their latest service records
        $employees = $query->get()->map(function($employee) {
            // Get the latest service record for this employee
            $latestServiceRecord = DB::table('service_records')
                ->where('employee_id', $employee->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            // Use updated_at if available, otherwise fallback to created_at
            $serviceRecordTime = $latestServiceRecord
                ? ($latestServiceRecord->updated_at ?? $latestServiceRecord->created_at)
                : null;

            $appointmentTime = $employee->updated_at ?? $employee->created_at;

            // Set the most recent update time
            $employee->latest_update = $serviceRecordTime && $serviceRecordTime > $appointmentTime
                ? $serviceRecordTime
                : $appointmentTime;

            return $employee;
        });
        
        // Sort by the most recent update time
        $employees = $employees->sortByDesc('latest_update')->values();
        
        // Paginate the results
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginatedEmployees = new \Illuminate\Pagination\LengthAwarePaginator(
            $employees->forPage($currentPage, $perPage),
            $employees->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $typeLabels = config('appointment_types');
        return view('Pages.Service_records.service_records', [
            'paginatedEmployees' => $paginatedEmployees,
            'employees' => $paginatedEmployees,
            'typeLabels' => $typeLabels
        ]);
    }
}
