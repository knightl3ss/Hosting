<?php

namespace App\Http\Controllers\ServiceRecordController;

use App\Http\Controllers\Controller;
use App\Models\AppointmentModel\Appointment;
use App\Models\ServiceRecordModel\RecordPurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RecordPurposeController extends Controller
{
    /**
     * Display the record purpose view.
     */
    public function index($id)
    {
        // Fetch the employee by ID (assuming Employee model is Appointment)
        $employee = Appointment::findOrFail($id);
        // Get the purpose history for this employee
        $purposeHistory = RecordPurpose::where('employee_id', $id)->orderBy('created_at', 'desc')->get();
        $typeLabels = config('appointment_types');
        return view('Pages.Service_records.record_purpose', compact('employee', 'purposeHistory', 'typeLabels'));
    }

    /**
     * Handle storing the record purpose form submission.
     */
    public function store(Request $request)
    {
        // Validate input (example: you can adjust the rules as needed)
        $validated = $request->validate([
            'employee_id' => 'required',
            'purpose_type' => 'required|string|max:255',
            'purpose_details' => 'nullable|string',
            'other_purpose' => 'nullable|string',
            'requested_date' => 'required|date',
            'item_no' => 'nullable|string',
        ]);

        // Determine the final purpose
        $purpose = $validated['purpose_type'] === 'other' && !empty($validated['other_purpose'])
            ? $validated['other_purpose']
            : $validated['purpose_type'];

        // Create record purpose data array
        $recordPurposeData = [
            'employee_id' => $validated['employee_id'],
            'purpose_type' => $validated['purpose_type'],
            'purpose' => $purpose,
            'purpose_details' => $validated['purpose_details'] ?? null,
            'requested_date' => $validated['requested_date'],
        ];

        // Check if item_no field exists and was provided
        if (Schema::hasColumn('record_purposes', 'item_no') && isset($validated['item_no'])) {
            $recordPurposeData['item_no'] = $validated['item_no'];
        }

        // Save to the database
        \App\Models\ServiceRecordModel\RecordPurpose::create($recordPurposeData);

        return redirect()->back()->with('success', 'Record purpose submitted successfully.');
    }

    /**
     * Update the status of a record purpose to Completed.
     */
    public function updateStatus(Request $request, $id)
    {
        $purpose = \App\Models\ServiceRecordModel\RecordPurpose::findOrFail($id);
        $purpose->status = 'Completed';
        $purpose->save();
        
        return redirect()->back()->with('success', 'Purpose status updated to Completed.');
    }
}
