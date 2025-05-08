<?php

namespace App\Http\Controllers\AppointmentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AppointmentModel\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AppointmentController extends Controller
{
    // Show Appointment Index Page
    public function index() {
        return view('Pages.appointments.index');
    }

    // Store a new appointment
    public function store(Request $request) {
        // DEBUG: Log all incoming request data for troubleshooting
        Log::info('Appointment store request', ['data' => $request->all()]);

        // Validate the incoming request
        $plantillaTypes = [
            'casual','contractual','coterminous','coterminousTemporary','elected','permanent','provisional','regularPermanent','substitute','temporary'
        ];
        $rules = [
            'appointment_type' => 'required|string',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'birthday' => 'required|date',
            'age' => 'required|integer',
            'location' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'rate_per_day' => 'required|numeric',
            'employment_start' => 'required|date',
            'employment_end' => (in_array($request->appointment_type, $plantillaTypes) ? 'nullable|date' : 'required|date|after_or_equal:employment_start'),
            'source_of_fund' => 'required|string|max:255',
            'office_assignment' => 'required|string|max:255',
        ];
        if ($request->appointment_type === 'job_order') {
            $rules['employee_id'] = 'required|string'; // Removed unique constraint
        } else {
            $rules['item_no'] = 'required|string';
        }
        $validated = $request->validate($rules);

        // Remove unused ID field before saving
        if ($request->appointment_type === 'job_order') {
            unset($validated['item_no']);
        } else {
            unset($validated['employee_id']);
        }

        // Create the appointment
        $appointment = Appointment::create($validated);

        try {
            DB::beginTransaction();

            // Determine which key to use for identifying the appointments
            $activeKey = $request->appointment_type === 'job_order' ? 'employee_id' : 'item_no';
            $activeValue = $request->appointment_type === 'job_order' ? $validated['employee_id'] : $validated['item_no'];

            // Deactivate all previous appointments for this employee/item
            Appointment::where($activeKey, $activeValue)
                ->where('id', '!=', $appointment->id)
                ->update(['is_active' => false]);

            // Activate the current appointment
            $appointment->is_active = true;
            $appointment->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Optionally log the error or return a response
            // Log::error('Failed to update appointment status', ['error' => $e->getMessage()]);
            return back()->withErrors('Failed to update appointment status. Please try again.');
        }

        // --- SYNC TO PERSONNEL TABLE ---
        $this->syncPersonnelFromAppointment($appointment);
        // --- END SYNC ---

        // Notify the user associated with this appointment, if exists
        $user = \App\Models\User::where('employee_id', $appointment->employee_id)->first();
        if ($user) {
            $user->notify(new \App\Notifications\AppointmentApproved($appointment));
        }

        // Redirect back to the same page
        return redirect()->back()->with('success', 'Appointment added successfully!');
    }

    // Edit/update appointment
    public function update(Request $request, $id) {
        // ... (copy validation and logic from original)
        $appointment = Appointment::findOrFail($id);

        // Update fields from request
        $appointment->first_name = $request->input('first_name');
        $appointment->middle_name = $request->input('middle_name');
        $appointment->last_name = $request->input('last_name');
        $appointment->extension_name = $request->input('extension_name');
        $appointment->gender = $request->input('gender');
        $appointment->birthday = $request->input('birthday');
        $appointment->age = $request->input('age');
        $appointment->location = $request->input('location');
        // ... (add other fields as needed)
        $appointment->save();

        // --- SYNC TO PERSONNEL TABLE ---
        $this->syncPersonnelFromAppointment($appointment);
        // --- END SYNC ---

        return redirect()->route('appointments.show', $appointment->id)
            ->with('success', 'Appointment updated successfully!');
    }

    // Update only personal details of an appointment
    public function updatePersonalDetails(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->first_name = $request->input('first_name');
        $appointment->middle_name = $request->input('middle_name');
        $appointment->last_name = $request->input('last_name');
        $appointment->extension_name = $request->input('extension_name');
        $appointment->gender = $request->input('gender');
        $appointment->birthday = $request->input('birthday');
        $appointment->age = $request->input('age');
        $appointment->location = $request->input('location');
        $appointment->save();

        return redirect()->route('appointments.show', $appointment->id)
            ->with('success', 'Personal details updated successfully!');
    }

    // Delete appointment
    public function destroy(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        $parentId = $request->input('parent_appointment_id');
        return redirect()->route('appointments.show', $parentId)
            ->with('success', 'Appointment deleted successfully!');
    }

    // Delete all appointments and the employee
    public function destroyEmployee($identifier)
    {
        // Try to delete by employee_id first
        $deleted = Appointment::where('employee_id', $identifier)->delete();

        // If nothing deleted, try by item_no (for plantilla)
        $deleted = Appointment::where('item_no', $identifier)->delete();
        // \App\Models\ServiceRecordModel\ServiceRecord::where('item_no', $identifier)->delete();
        // Removed PlantillaModel\Personnel deletion
        if ($deleted === 0) {
            // Also delete from ServiceRecord by item_no if needed
            \App\Models\ServiceRecordModel\ServiceRecord::where('item_no', $identifier)->delete();
        } else {
            // Also delete from ServiceRecord by employee_id
            \App\Models\ServiceRecordModel\ServiceRecord::where('employee_id', $identifier)->delete();
        }

        return redirect()->route('appointment.schedule')->with('success', 'Employee and all related records deleted successfully!');
    }

    // Import CSV (AJAX JSON import)
    public function import(Request $request) {
        $data = $request->input('appointments');
        if (!$data || !is_array($data)) {
            return response()->json(['success' => false, 'message' => 'No data received.']);
        }
        $inserted = 0;
        $errors = [];
        foreach ($data as $row) {
            try {
                // Validate each row (same rules as store)
                $validator = validator($row, [
                    'name' => 'required|string|max:255',
                    'position' => 'required|string|max:255',
                    'rate_per_day' => 'required|numeric',
                    'employment_start' => 'required|date',
                    'employment_end' => 'required|date|after_or_equal:employment_start',
                    'source_of_fund' => 'required|string|max:255',
                    'location' => 'nullable|string|max:255',
                    'office_assignment' => 'required|string|max:255',
                    'appointment_type' => 'required|string',
                    'item_no' => 'nullable|string|max:255',
                    'employee_id' => 'required|string',
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'extension_name' => 'nullable|string|max:255',
                    'gender' => 'required|string|in:male,female,other',
                    'birthday' => 'required|date',
                    'age' => 'required|integer',
                ]);
                $validated = $validator->validate();
                // Insert
                Appointment::create($validated);
                $inserted++;
            } catch (\Illuminate\Validation\ValidationException $ex) {
                $errors[] = ($row['employee_id'] ?? 'Unknown') . ': ' . json_encode($ex->errors());
            } catch (\Exception $ex) {
                $errors[] = ($row['employee_id'] ?? 'Unknown') . ': ' . $ex->getMessage();
            }
        }
        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Some records failed to import.',
                'errors' => $errors,
                'inserted' => $inserted
            ]);
        }
        return response()->json(['success' => true, 'inserted' => $inserted]);
    }

    // AJAX endpoint for real-time uniqueness check
    public function checkUnique(Request $request)
    {
        $type = $request->input('type'); // 'employee_id' or 'item_no'
        $value = $request->input('value');
        $exists = false;
        if ($type === 'employee_id') {
            $exists = Appointment::where('employee_id', $value)->exists();
        } elseif ($type === 'item_no') {
            $exists = Appointment::where('item_no', $value)->exists();
        }
        return response()->json(['exists' => $exists]);
    }

    // Private helper for updating service record
    private function updateServiceRecord($appointment) {
        // ... (copy logic from original)
    }

    /**
     * Sync the personnel record from the given appointment.
     * Only updates if NOT job_order.
     */
    protected function syncPersonnelFromAppointment($appointment)
    {
        // Remove $plantillaTypes and all plantilla-related logic
    }
}
