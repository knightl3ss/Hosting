<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentModel\Appointment;
use App\Models\ServiceRecordModel\RecordPurpose;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Apply filters to the query based on request parameters
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by appointment type if provided
        if ($request->filled('appointment_type')) {
            $query->where('appointment_type', $request->appointment_type);
        }
        
        // Filter by office assignment if provided
        if ($request->filled('office_assignment')) {
            $query->where('office_assignment', 'LIKE', '%' . $request->office_assignment . '%');
        }
        
        // Filter by position if provided
        if ($request->filled('position')) {
            $query->where('position', 'LIKE', '%' . $request->position . '%');
        }
        
        return $query;
    }
    /**
     * Generate report for male employees
     */
    public function maleEmployees(Request $request)
    {
        $query = Appointment::where('gender', 'male');
        $this->applyFilters($query, $request);
        $maleEmployees = $query->get();
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $maleEmployees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'Male Employees Report',
            'icon' => 'fa-male',
            'iconColor' => 'text-primary'
        ]);
    }

    /**
     * Generate report for female employees
     */
    public function femaleEmployees(Request $request)
    {
        $query = Appointment::where('gender', 'female');
        $this->applyFilters($query, $request);
        $femaleEmployees = $query->get();
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $femaleEmployees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'Female Employees Report',
            'icon' => 'fa-female',
            'iconColor' => 'text-danger'
        ]);
    }

    /**
     * Generate report for in-service employees
     */
    public function inServiceEmployees(Request $request)
    {
        $inServiceEmployees = $this->getEmployeesByServiceStatus('In Service', $request);
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $inServiceEmployees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'In Service Employees Report',
            'icon' => 'fa-user-check',
            'iconColor' => 'text-success'
        ]);
    }

    /**
     * Generate report for employees on suspension
     */
    public function suspensionEmployees(Request $request)
    {
        $suspensionEmployees = $this->getEmployeesByServiceStatus('Suspension', $request);
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $suspensionEmployees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'Employees on Suspension Report',
            'icon' => 'fa-user-clock',
            'iconColor' => 'text-warning'
        ]);
    }

    /**
     * Generate report for not-in-service employees
     */
    public function notInServiceEmployees(Request $request)
    {
        $notInServiceEmployees = $this->getEmployeesByServiceStatus('Not in Service', $request);
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $notInServiceEmployees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'Not in Service Employees Report',
            'icon' => 'fa-user-times',
            'iconColor' => 'text-danger'
        ]);
    }

    /**
     * Generate report for employees by years of service
     */
    public function serviceYears($group, Request $request)
    {
        $serviceGroups = $this->getServiceGroups($request);
        $appointmentTypes = $this->getAppointmentTypes();
        
        $groupLabels = [
            '10-plus' => '10 Years+',
            '5-9' => '5-9 Years',
            'below-5' => 'Below 5 Years'
        ];
        
        $groupIcons = [
            '10-plus' => 'fa-users',
            '5-9' => 'fa-user-friends',
            'below-5' => 'fa-user-plus'
        ];
        
        $groupColors = [
            '10-plus' => 'text-primary',
            '5-9' => 'text-success',
            'below-5' => 'text-info'
        ];
        
        $groupLabel = $groupLabels[$group] ?? 'Years of Service';
        $groupIcon = $groupIcons[$group] ?? 'fa-users';
        $groupColor = $groupColors[$group] ?? 'text-primary';
        
        $employees = $serviceGroups[$groupLabels[$group]] ?? collect();
        
        return view('Pages.Reports.employee_report', [
            'employees' => $employees,
            'appointmentTypes' => $appointmentTypes,
            'title' => 'Employees with ' . $groupLabel . ' Report',
            'icon' => $groupIcon,
            'iconColor' => $groupColor
        ]);
    }

    /**
     * Generate report for employees who have made requests
     */
    public function employeesWithRequests(Request $request)
    {
        // Get distinct employee IDs from record_purposes
        $employeeIds = RecordPurpose::distinct()->pluck('employee_id');
        
        // Fetch the complete employee records
        $query = Appointment::whereIn('id', $employeeIds);
        $this->applyFilters($query, $request);
        $employeesWithRequests = $query->get();
            
        $appointmentTypes = $this->getAppointmentTypes();
        
        return view('Pages.Reports.employees_with_requests_report', [
            'employees' => $employeesWithRequests,
            'appointmentTypes' => $appointmentTypes
        ]);
    }

    /**
     * Get employees by service status
     */
    private function getEmployeesByServiceStatus($status, Request $request)
    {
        $employeeIds = DB::table('service_records')
            ->select('employee_id')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('service_records')
                    ->groupBy('employee_id');
            })
            ->where('service_status', $status)
            ->pluck('employee_id');
            
        $query = Appointment::whereIn('id', $employeeIds);
        $this->applyFilters($query, $request);
        return $query->get();
    }

    /**
     * Get appointment types
     */
    private function getAppointmentTypes()
    {
        return config('appointment_types');
    }

    /**
     * Get service groups
     */
    private function getServiceGroups(Request $request = null)
    {
        $query = Appointment::query();
        if ($request) {
            $this->applyFilters($query, $request);
        }
        $employees = $query->get();
        
        $serviceGroups = [
            '10 Years+' => collect(),
            '5-9 Years' => collect(),
            'Below 5 Years' => collect()
        ];
        
        foreach ($employees as $employee) {
            $dateHired = Carbon::parse($employee->employment_start ?? $employee->date_hired);
            $yearsOfService = $dateHired->diffInYears(now());
            
            if ($yearsOfService >= 10) {
                $serviceGroups['10 Years+']->push($employee);
            } elseif ($yearsOfService >= 5 && $yearsOfService < 10) {
                $serviceGroups['5-9 Years']->push($employee);
            } else {
                $serviceGroups['Below 5 Years']->push($employee);
            }
        }
        
        return $serviceGroups;
    }
}
