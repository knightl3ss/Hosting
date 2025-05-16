<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Employee;
use App\Http\Requests\ServiceRecordRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceRecordController extends Controller
{
    public function store(ServiceRecordRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            $serviceRecord = new ServiceRecord();
            $serviceRecord->employee_id = $employee->id;
            $serviceRecord->date_from = $request->date_from;
            $serviceRecord->date_to = $request->date_to;
            $serviceRecord->designation = $request->designation;
            $serviceRecord->status = $request->status;
            $serviceRecord->payment_frequency = $request->payment_frequency;
            $serviceRecord->salary = (string)$request->salary;
            $serviceRecord->station = $request->station;
            $serviceRecord->service_status = $request->service_status;
            $serviceRecord->separation_date = $request->separation_date;
            
            $serviceRecord->save();
            
            return redirect()->back()->with('success', 'Service record added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding service record: ' . $e->getMessage());
        }
    }

    public function update(ServiceRecordRequest $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $serviceRecord = ServiceRecord::findOrFail($request->record_id);
            
            // Validate that the record belongs to the employee
            if ($serviceRecord->employee_id !== $employee->id) {
                throw new \Exception('Invalid service record for this employee.');
            }
            
            $serviceRecord->date_from = $request->date_from;
            $serviceRecord->date_to = $request->date_to;
            $serviceRecord->designation = $request->designation;
            $serviceRecord->status = $request->status;
            $serviceRecord->payment_frequency = $request->payment_frequency;
            $serviceRecord->salary = (string)$request->salary;
            $serviceRecord->station = $request->station;
            $serviceRecord->service_status = $request->service_status;
            $serviceRecord->separation_date = $request->separation_date;
            
            $serviceRecord->save();
            
            return redirect()->back()->with('success', 'Service record updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating service record: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $serviceRecord = ServiceRecord::findOrFail($id);
            
            // Check if this is the latest record
            $latestRecord = ServiceRecord::where('employee_id', $serviceRecord->employee_id)
                ->orderBy('date_to', 'desc')
                ->first();
                
            if ($latestRecord && $latestRecord->id !== $serviceRecord->id) {
                throw new \Exception('Cannot delete a service record that is not the latest.');
            }
            
            $serviceRecord->delete();
            
            return redirect()->back()->with('success', 'Service record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting service record: ' . $e->getMessage());
        }
    }
} 