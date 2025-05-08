<?php

namespace App\Http\Controllers\ServiceRecordController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentModel\Appointment;

class ServiceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtering logic moved to ServiceRecordFilterController
        // You may call the filter method from ServiceRecordFilterController or redirect
        $typeLabels = config('appointment_types');
        return app(ServiceRecordFilterController::class)->filter($request, $typeLabels);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // TODO: Return a view for creating a new service record
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Handle storing a new service record
    }

    /**
     * Store a new service record entry
     */
    public function storeServiceRecord(Request $request, $employeeId)
    {
        $appointment = Appointment::findOrFail($employeeId);
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'designation' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'salary' => 'required|numeric',
            'payment_frequency' => 'required|string|max:50',
            'station' => 'required|string|max:255',
            'separation_date' => 'nullable|string|max:255',
            'service_status' => 'required|string|max:50',
        ]);
        $validated['employee_id'] = $appointment->id;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        DB::table('service_records')->insert($validated);
        $appointment->touch();
        return redirect()->route('Employee_records', ['id' => $employeeId])
            ->with('success', 'Service record entry added successfully.');
    }

    /**
     * Get available payment frequencies based on employee status
     */
    public function getAvailableFrequencies($appointmentType)
    {
        if ($appointmentType === 'job_order') {
            return ['Daily'];
        }
        return ['Monthly', 'Annum'];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // TODO: Show a specific service record
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // TODO: Return a view for editing a service record
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // TODO: Handle updating a service record
    }

    /**
     * Update a service record entry
     */
    public function updateServiceRecord(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'record_id' => 'required|integer|exists:service_records,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'designation' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'salary' => 'required|numeric',
            'payment_frequency' => 'required|string|max:50',
            'station' => 'required|string|max:255',
            'separation_date' => 'nullable|string|max:255',
            'service_status' => 'required|string|max:50',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['updated_at'] = now();

        unset($validated['record_id']);

        DB::table('service_records')
            ->where('id', $request->input('record_id'))
            ->update($validated);
        $appointment = Appointment::find($employeeId);
        if ($appointment) {
            $appointment->touch();
        }
        return redirect()->route('Employee_records', ['id' => $employeeId])
            ->with('success', 'Service record entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the service record
            $serviceRecord = DB::table('service_records')->where('id', $id)->first();
            
            if (!$serviceRecord) {
                return redirect()->back()->with('error', 'Service record not found.');
            }

            // Get the employee ID before deleting
            $employeeId = $serviceRecord->employee_id;

            // Delete the service record
            DB::table('service_records')->where('id', $id)->delete();

            // Update the appointment's updated_at timestamp
            $appointment = Appointment::find($employeeId);
            if ($appointment) {
                $appointment->touch();
            }

            return redirect()->back()->with('success', 'Service record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete service record: ' . $e->getMessage());
        }
    }
}
