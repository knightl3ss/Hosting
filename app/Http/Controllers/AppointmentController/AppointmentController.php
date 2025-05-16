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
            'rate_per_day' => 'required|string|max:255',
            'employment_start' => 'required|date',
            'employment_end' => (in_array($request->appointment_type, $plantillaTypes) ? 'nullable|date' : 'required|date|after_or_equal:employment_start'),
            'source_of_fund' => 'required|string|max:255',
            'office_assignment' => 'required|string|max:255',
        ];
        if ($request->appointment_type === 'job_order') {
            $rules['employee_id'] = ['required', 'string', 'regex:/^[a-zA-Z0-9\-]+$/'];
        } else {
            $rules['item_no'] = ['required', 'string', 'regex:/^[a-zA-Z0-9\-]+$/'];
            // Also validate employee_id for non-job orders, but it's not required as it will be copied from item_no
            $rules['employee_id'] = ['nullable', 'string', 'regex:/^[a-zA-Z0-9\-]+$/'];
        }

        $messages = [
            'employee_id.required' => 'Employee ID is required for Job Order appointments.',
            'employee_id.regex' => 'Employee ID can only contain letters, numbers, and hyphens.',
            'item_no.required' => 'Item No is required for non-Job Order appointments.',
            'item_no.regex' => 'Item No can only contain letters, numbers, and hyphens.',
        ];

        $validated = $request->validate($rules, $messages);

        // For non-job orders, ensure employee_id matches item_no
        if ($request->appointment_type !== 'job_order') {
            $validated['employee_id'] = $validated['item_no'];
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
        try {
            DB::beginTransaction();
            
            Log::info('Starting employee deletion process', ['identifier' => $identifier]);
            
            // The issue is that employee_id and item_no are encrypted in the database
            // We need to get all appointments first and then delete them one by one
            $appointments = Appointment::all();
            $deletedCount = 0;
            
            foreach ($appointments as $appointment) {
                // Check if this appointment matches our identifier
                // We need to compare decrypted values
                if ($appointment->employee_id == $identifier || $appointment->item_no == $identifier) {
                    $appointmentId = $appointment->id;
                    Log::info('Found matching appointment', ['id' => $appointmentId]);
                    
                    // Delete the appointment by ID (which is not encrypted)
                    $appointment->delete();
                    $deletedCount++;
                }
            }
            
            Log::info('Deleted appointments', ['count' => $deletedCount]);
            
            // For service records, we need the same approach if they use encryption
            if (class_exists('\App\Models\ServiceRecordModel\ServiceRecord')) {
                $serviceRecords = \App\Models\ServiceRecordModel\ServiceRecord::all();
                $deletedServiceRecords = 0;
                
                foreach ($serviceRecords as $record) {
                    // Check if the service record has the same methods for accessing encrypted attributes
                    $recordEmployeeId = method_exists($record, 'getAttribute') ? $record->employee_id : $record->getRawOriginal('employee_id');
                    $recordItemNo = method_exists($record, 'getAttribute') ? $record->item_no : $record->getRawOriginal('item_no');
                    
                    if ($recordEmployeeId == $identifier || $recordItemNo == $identifier) {
                        $record->delete();
                        $deletedServiceRecords++;
                    }
                }
                
                Log::info('Deleted service records', ['count' => $deletedServiceRecords]);
            }
            
            // Check if Personnel model exists and delete from there too
            if (class_exists('\App\Models\PlantillaModel\Personnel')) {
                $personnel = \App\Models\PlantillaModel\Personnel::all();
                $deletedPersonnel = 0;
                
                foreach ($personnel as $person) {
                    // Check if the personnel record has the same methods for accessing encrypted attributes
                    $personEmployeeId = method_exists($person, 'getAttribute') ? $person->employee_id : $person->getRawOriginal('employee_id');
                    $personItemNo = method_exists($person, 'getAttribute') ? $person->item_no : $person->getRawOriginal('item_no');
                    
                    if ($personEmployeeId == $identifier || $personItemNo == $identifier) {
                        $person->delete();
                        $deletedPersonnel++;
                    }
                }
                
                Log::info('Deleted personnel records', ['count' => $deletedPersonnel]);
            }
            
            // Check if any User is associated with this employee and update their status if needed
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                $userEmployeeId = method_exists($user, 'getAttribute') ? $user->employee_id : $user->getRawOriginal('employee_id');
                
                if ($userEmployeeId == $identifier) {
                    Log::info('Found associated user account', ['user_id' => $user->id]);
                    // Option 1: Update user status to inactive
                    // $user->status = 'inactive';
                    // $user->save();
                    
                    // Option 2: Delete the user account
                    // $user->delete();
                }
            }
            
            DB::commit();
            Log::info('Employee deletion completed successfully');
            
            if ($deletedCount > 0) {
                return redirect()->route('appointment.schedule')->with('success', 'Employee and all related records deleted successfully!');
            } else {
                return redirect()->route('appointment.schedule')->with('warning', 'No matching employee records found for deletion.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee records', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('appointment.schedule')->with('error', 'Failed to delete employee records: ' . $e->getMessage());
        }
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
                    'item_no' => 'nullable|string|regex:/^[a-zA-Z0-9\-]+$/',
                    'employee_id' => 'required|string|regex:/^[a-zA-Z0-9\-]+$/',
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
        $excludeId = $request->input('exclude_id'); // for edit form, exclude the current record
        
        $exists = false;
        $query = Appointment::query();
        
        // Add condition based on type
        if ($type === 'employee_id') {
            $query->where('employee_id', $value);
        } elseif ($type === 'item_no') {
            $query->where('item_no', $value);
        } else {
            return response()->json(['error' => 'Invalid type specified'], 400);
        }
        
        // Exclude current record if editing
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        // Check only active records
        $query->where('is_active', true);
        
        $exists = $query->exists();
        
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
