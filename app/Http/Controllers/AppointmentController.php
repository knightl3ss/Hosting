<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function store(AppointmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::create($request->validated());

            DB::commit();

            return redirect()->route('appointment.schedule')
                ->with('success', 'Appointment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create appointment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(AppointmentRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::findOrFail($id);
            $appointment->update($request->validated());

            DB::commit();

            return redirect()->route('appointment.schedule')
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update appointment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'appointments' => 'required|array',
            'appointments.*.appointment_type' => 'required|in:casual,contractual,coterminous,coterminousTemporary,elected,permanent,provisional,regularPermanent,substitute,temporary,job_order',
            'appointments.*.first_name' => 'required|string|max:255',
            'appointments.*.last_name' => 'required|string|max:255',
            'appointments.*.middle_name' => 'nullable|string|max:255',
            'appointments.*.extension_name' => 'nullable|string|max:255',
            'appointments.*.gender' => 'required|in:male,female',
            'appointments.*.birthday' => 'required|date',
            'appointments.*.age' => 'required|integer|min:18|max:65',
            'appointments.*.position' => 'required|string|max:255',
            'appointments.*.rate_per_day' => 'required|string|max:255',
            'appointments.*.employment_start' => 'required|date',
            'appointments.*.employment_end' => 'required|date|after:appointments.*.employment_start',
            'appointments.*.source_of_fund' => 'required|string|max:255',
            'appointments.*.location' => 'nullable|string|max:255',
            'appointments.*.office_assignment' => 'required|string|max:255',
            'appointments.*.employee_id' => 'required|string',
            'appointments.*.item_no' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $errors = [];
            $successCount = 0;
            $existingEmployeeIds = Appointment::pluck('employee_id')->toArray();
            $existingItemNos = Appointment::pluck('item_no')->toArray();
            $batchEmployeeIds = [];
            $batchItemNos = [];

            // First pass: check for duplicates within the batch and against database
            foreach ($request->appointments as $index => $data) {
                // Check for duplicate employee_id within the batch
                if (isset($data['employee_id']) && $data['employee_id']) {
                    if (in_array($data['employee_id'], $batchEmployeeIds)) {
                        $errors[] = "Row " . ($index + 1) . ": Duplicate Employee ID '{$data['employee_id']}' found in the batch";
                        continue;
                    } else {
                        $batchEmployeeIds[] = $data['employee_id'];
                    }
                    
                    // Check against database
                    if (in_array($data['employee_id'], $existingEmployeeIds)) {
                        $errors[] = "Row " . ($index + 1) . ": Employee ID '{$data['employee_id']}' already exists in the database";
                        continue;
                    }
                }
                
                // Check for duplicate item_no within the batch
                if (isset($data['item_no']) && $data['item_no']) {
                    if (in_array($data['item_no'], $batchItemNos)) {
                        $errors[] = "Row " . ($index + 1) . ": Duplicate Item No '{$data['item_no']}' found in the batch";
                        continue;
                    } else {
                        $batchItemNos[] = $data['item_no'];
                    }
                    
                    // Check against database
                    if (in_array($data['item_no'], $existingItemNos)) {
                        $errors[] = "Row " . ($index + 1) . ": Item No '{$data['item_no']}' already exists in the database";
                        continue;
                    }
                }
            }
            
            // If we have errors from the first pass, fail early
            if (!empty($errors)) {
                throw new \Exception(implode("\n", $errors));
            }
            
            // Second pass: process valid records
            foreach ($request->appointments as $index => $data) {
                try {
                    // Validate employee_id format based on appointment type
                    if ($data['appointment_type'] === 'job_order') {
                        if (!preg_match('/^JO-\d{4}-\d{3}$/', $data['employee_id'])) {
                            throw new \Exception("Row " . ($index + 1) . ": Invalid Job Order ID format");
                        }
                    } else {
                        if (!preg_match('/^\d{4}-\d{3}$/', $data['item_no'])) {
                            throw new \Exception("Row " . ($index + 1) . ": Invalid Item No format");
                        }
                    }

                    // Validate employment duration for job orders
                    if ($data['appointment_type'] === 'job_order') {
                        $start = Carbon::parse($data['employment_start']);
                        $end = Carbon::parse($data['employment_end']);
                        if ($end->diffInMonths($start) > 6) {
                            // Instead of silently modifying, throw an exception
                            throw new \Exception("Row " . ($index + 1) . ": Job Order employment duration cannot exceed 6 months");
                        }
                    }

                    Appointment::create($data);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            if (count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some records failed to import.',
                    'errors' => $errors,
                    'successCount' => $successCount
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'All records imported successfully.',
                'successCount' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ]);
        }
    }
} 