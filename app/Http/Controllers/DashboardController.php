<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use App\Models\AppointmentModel\JobList;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceRecordModel\RecordPurpose;
use App\Models\PlantillaModel\Personnel;

/**
 * DashboardController
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard with real statistics
     */
    public function index(Request $request)
    {
        // Get the selected appointment type from request
        $selectedAppointmentType = $request->input('appointment_type');

        // Count admins (users with admin role)
        $adminCount = User::where('role', 'admin')->count();

        // Base query for appointments
        $appointmentQuery = Appointment::query();
        if ($selectedAppointmentType) {
            $appointmentQuery->where('appointment_type', $selectedAppointmentType);
        }

        // Count male and female employees from appointments (unique by employee_id)
        $maleCount = (clone $appointmentQuery)->where('gender', 'male')->count();
        $femaleCount = (clone $appointmentQuery)->where('gender', 'female')->count();

        // Base query for service records with left join to include all employees
        $serviceRecordQuery = DB::table('appointments as a')
            ->leftJoin('service_records as sr', function($join) {
                $join->on('a.id', '=', 'sr.employee_id')
                    ->whereRaw('sr.id IN (SELECT MAX(id) FROM service_records GROUP BY employee_id)');
            });

        if ($selectedAppointmentType) {
            $serviceRecordQuery->where('a.appointment_type', $selectedAppointmentType);
        }

        // Get service status counts from the latest service records
        $inServiceCount = (clone $serviceRecordQuery)
            ->where('sr.service_status', 'In Service')
            ->count();

        $suspensionCount = (clone $serviceRecordQuery)
            ->where('sr.service_status', 'Suspension')
            ->count();

        $notInServiceCount = (clone $serviceRecordQuery)
            ->where(function($query) {
                $query->where('sr.service_status', 'Not in Service')
                    ->orWhereNull('sr.service_status');
            })
            ->count();

        // Get employee data for modals with strict filtering
        $maleEmployees = (clone $appointmentQuery)
            ->where('gender', 'male')
            ->select('id', 'first_name', 'last_name', 'position', 'appointment_type', 'office_assignment')
            ->get();

        $femaleEmployees = (clone $appointmentQuery)
            ->where('gender', 'female')
            ->select('id', 'first_name', 'last_name', 'position', 'appointment_type', 'office_assignment')
            ->get();

        // Get employees by service status with strict filtering
        $inServiceEmployees = DB::table('appointments as a')
            ->leftJoin('service_records as sr', function($join) {
                $join->on('a.id', '=', 'sr.employee_id')
                    ->whereRaw('sr.id IN (SELECT MAX(id) FROM service_records GROUP BY employee_id)');
            })
            ->where('sr.service_status', 'In Service')
            ->when($selectedAppointmentType, function($query) use ($selectedAppointmentType) {
                return $query->where('a.appointment_type', $selectedAppointmentType);
            })
            ->select('a.id', 'a.first_name', 'a.last_name', 'a.position', 'a.appointment_type', 'a.office_assignment', 'a.gender')
            ->get();

        $suspensionEmployees = DB::table('appointments as a')
            ->leftJoin('service_records as sr', function($join) {
                $join->on('a.id', '=', 'sr.employee_id')
                    ->whereRaw('sr.id IN (SELECT MAX(id) FROM service_records GROUP BY employee_id)');
            })
            ->where('sr.service_status', 'Suspension')
            ->when($selectedAppointmentType, function($query) use ($selectedAppointmentType) {
                return $query->where('a.appointment_type', $selectedAppointmentType);
            })
            ->select('a.id', 'a.first_name', 'a.last_name', 'a.position', 'a.appointment_type', 'a.office_assignment', 'a.gender')
            ->get();

        $notInServiceEmployees = DB::table('appointments as a')
            ->leftJoin('service_records as sr', function($join) {
                $join->on('a.id', '=', 'sr.employee_id')
                    ->whereRaw('sr.id IN (SELECT MAX(id) FROM service_records GROUP BY employee_id)');
            })
            ->where(function($query) {
                $query->where('sr.service_status', 'Not in Service')
                    ->orWhereNull('sr.service_status');
            })
            ->when($selectedAppointmentType, function($query) use ($selectedAppointmentType) {
                return $query->where('a.appointment_type', $selectedAppointmentType);
            })
            ->select('a.id', 'a.first_name', 'a.last_name', 'a.position', 'a.appointment_type', 'a.office_assignment', 'a.gender')
            ->get();

        // Count permanent employees
        $permanentCount = Appointment::where('appointment_type', 'permanent')->count();

        // Count temporary employees
        $temporaryCount = Appointment::where('appointment_type', 'temporary')->count();
        
        // Count job order employees
        $jobOrderCount = Appointment::where('appointment_type', 'job_order')->count();

        // Count job listings
        $jobListCount = JobList::count();

        // Count transactions/service records from the service_records table
        $transactionCount = DB::table('service_records')->count();

        // Check if service_records table exists
        $serviceRecordsExist = Schema::hasTable('service_records');

        // Get recent transactions for the table
        if ($serviceRecordsExist) {
            $recentTransactions = DB::table('service_records as sr')
                ->join('appointments as a', 'sr.employee_id', '=', 'a.id')
                ->select(
                    'sr.id',
                    DB::raw("CONCAT(a.first_name, ' ', a.last_name) as employee_name"),
                    'sr.date_from as transaction_date',
                    'a.gender',
                    'sr.status',
                    'sr.designation',
                    DB::raw("CASE 
                        WHEN sr.service_status = 'Permanent' THEN 'Permanent Employee' 
                        ELSE sr.service_status END as service_status")
                )
                ->orderByDesc('sr.id')
                ->limit(5)
                ->get();
        } else {
            $recentTransactions = collect();
        }

        // --- Custom: Employees by Years of Service ---
        $now = now();
        $appointments = $appointmentQuery->get();
        $appointments = $appointments->map(function ($appt) use ($now) {
            $appt->years_of_service = $appt->employment_start ? $now->diffInYears($appt->employment_start) : 0;
            return $appt;
        });
        $serviceGroups = [
            '10 Years+' => $appointments->filter(fn($a) => $a->years_of_service >= 10),
            '5-9 Years' => $appointments->filter(fn($a) => $a->years_of_service >= 5 && $a->years_of_service < 10),
            'Below 5 Years' => $appointments->filter(fn($a) => $a->years_of_service < 5),
        ];

        // --- Custom: Employees who have made requests ---
        $requestedEmployeeIds = RecordPurpose::distinct()->pluck('employee_id');
        $employeesWithRequests = $appointmentQuery->whereIn('id', $requestedEmployeeIds)->get();

        // Fetch recent completed record purposes with strict filtering
        $recentCompletedPurposes = RecordPurpose::with(['employee' => function($query) use ($selectedAppointmentType) {
                $query->select('id', 'first_name', 'last_name');
                if ($selectedAppointmentType) {
                    $query->where('appointment_type', $selectedAppointmentType);
                }
            }])
            ->where('status', 'Completed')
            ->when($selectedAppointmentType, function($query) use ($selectedAppointmentType) {
                return $query->whereHas('employee', function($q) use ($selectedAppointmentType) {
                    $q->where('appointment_type', $selectedAppointmentType);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get appointment types for filter dropdown
        $appointmentTypes = config('appointment_types');

        return view('Pages.dashboard', compact(
            'adminCount',
            'maleCount',
            'femaleCount',
            'inServiceCount',
            'suspensionCount',
            'notInServiceCount',
            'permanentCount',
            'temporaryCount',
            'jobOrderCount',
            'jobListCount',
            'transactionCount',
            'recentTransactions',
            'serviceGroups',
            'employeesWithRequests',
            'recentCompletedPurposes',
            'appointmentTypes',
            'selectedAppointmentType',
            'maleEmployees',
            'femaleEmployees',
            'inServiceEmployees',
            'suspensionEmployees',
            'notInServiceEmployees'
        ));
    }
}