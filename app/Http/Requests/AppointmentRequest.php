<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Appointment;

class AppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'appointment_type' => 'required|in:casual,contractual,coterminous,coterminousTemporary,elected,permanent,provisional,regularPermanent,substitute,temporary,job_order',
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'extension_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date|before:today',
            'age' => 'required|integer|min:18|max:65',
            'position' => 'required|string|min:3|max:255|regex:/^[a-zA-Z\s\-\']+$/',
            'rate_per_day' => 'required|string|max:255',
            'employment_start' => 'required|date',
            'source_of_fund' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'office_assignment' => 'required|string|max:255',
        ];

        // Add conditional validation for employee_id/item_no based on appointment type
        if ($this->input('appointment_type') === 'job_order') {
            $rules['employee_id'] = [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\-]+$/',
                Rule::unique('appointments', 'employee_id')->ignore($this->route('appointment')),
            ];
            
            // For job orders, employment_end must be after start date but not more than 6 months
            $rules['employment_end'] = [
                'required',
                'date',
                'after:employment_start',
                function ($attribute, $value, $fail) {
                    $start = Carbon::parse($this->input('employment_start'));
                    $end = Carbon::parse($value);
                    
                    if ($end->diffInMonths($start) > 6) {
                        $fail('Job Order employment duration cannot exceed 6 months.');
                    }
                },
            ];
        } else {
            $rules['item_no'] = [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9\-]+$/',
                Rule::unique('appointments', 'item_no')->ignore($this->route('appointment')),
            ];
            
            // For other appointment types, just ensure end date is after start date
            $rules['employment_end'] = 'required|date|after:employment_start';
        }

        return $rules;
    }
    
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check for duplicate IDs in batch submissions
            if ($this->has('appointments') && is_array($this->input('appointments'))) {
                $employeeIds = [];
                $itemNos = [];
                
                foreach ($this->input('appointments') as $index => $appointment) {
                    // Check for duplicate employee_id within the batch
                    if (isset($appointment['employee_id']) && $appointment['employee_id']) {
                        if (in_array($appointment['employee_id'], $employeeIds)) {
                            $validator->errors()->add(
                                "appointments.{$index}.employee_id", 
                                "Duplicate Employee ID found in the batch: {$appointment['employee_id']}"
                            );
                        } else {
                            $employeeIds[] = $appointment['employee_id'];
                            
                            // Check if this ID already exists in the database
                            $exists = Appointment::where('employee_id', $appointment['employee_id'])->exists();
                            if ($exists) {
                                $validator->errors()->add(
                                    "appointments.{$index}.employee_id", 
                                    "Employee ID already exists in the database: {$appointment['employee_id']}"
                                );
                            }
                        }
                    }
                    
                    // Check for duplicate item_no within the batch
                    if (isset($appointment['item_no']) && $appointment['item_no']) {
                        if (in_array($appointment['item_no'], $itemNos)) {
                            $validator->errors()->add(
                                "appointments.{$index}.item_no", 
                                "Duplicate Item No found in the batch: {$appointment['item_no']}"
                            );
                        } else {
                            $itemNos[] = $appointment['item_no'];
                            
                            // Check if this Item No already exists in the database
                            $exists = Appointment::where('item_no', $appointment['item_no'])->exists();
                            if ($exists) {
                                $validator->errors()->add(
                                    "appointments.{$index}.item_no", 
                                    "Item No already exists in the database: {$appointment['item_no']}"
                                );
                            }
                        }
                    }
                    
                    // Cross-check employment dates for job orders
                    if (isset($appointment['appointment_type']) && $appointment['appointment_type'] === 'job_order' &&
                        isset($appointment['employment_start']) && isset($appointment['employment_end'])) {
                        $start = Carbon::parse($appointment['employment_start']);
                        $end = Carbon::parse($appointment['employment_end']);
                        
                        if ($end->diffInMonths($start) > 6) {
                            $validator->errors()->add(
                                "appointments.{$index}.employment_end", 
                                "Job Order employment duration cannot exceed 6 months."
                            );
                        }
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'appointment_type.required' => 'Please select an appointment type.',
            'appointment_type.in' => 'Invalid appointment type selected.',
            
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes.',
            'extension_name.regex' => 'Extension name can only contain letters, spaces, hyphens, and apostrophes.',
            
            'gender.required' => 'Please select a gender.',
            'gender.in' => 'Invalid gender selected.',
            
            'birthday.required' => 'Date of birth is required.',
            'birthday.before' => 'Date of birth must be before today.',
            
            'age.required' => 'Age is required.',
            'age.min' => 'Age must be at least 18 years.',
            'age.max' => 'Age cannot exceed 65 years.',
            
            'position.required' => 'Position is required.',
            'position.min' => 'Position must be at least 3 characters long.',
            'position.regex' => 'Position can only contain letters, spaces, hyphens, and apostrophes.',
            
            'rate_per_day.required' => 'Rate per day is required.',
            
            'employment_start.required' => 'Employment start date is required.',
            'employment_end.required' => 'Employment end date is required.',
            'employment_end.after' => 'Employment end date must be after the start date.',
            
            'source_of_fund.required' => 'Source of fund is required.',
            'office_assignment.required' => 'Office assignment is required.',
            
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.regex' => 'Employee ID can only contain letters, numbers, and hyphens.',
            'item_no.required' => 'Item No is required.',
            'item_no.regex' => 'Item No can only contain letters, numbers, and hyphens.',
        ];
    }

    protected function prepareForValidation()
    {
        // Calculate age from birthday if provided
        if ($this->has('birthday')) {
            $birthday = Carbon::parse($this->birthday);
            $age = $birthday->age;
            $this->merge(['age' => $age]);
        }

        // Validate employment duration for job orders
        if ($this->input('appointment_type') === 'job_order' && 
            $this->has('employment_start') && 
            $this->has('employment_end')) {
            
            $start = Carbon::parse($this->employment_start);
            $end = Carbon::parse($this->employment_end);
            
            if ($end->diffInMonths($start) > 6) {
                $this->merge([
                    'employment_end' => $start->copy()->addMonths(6)->format('Y-m-d')
                ]);
            }
        }
        
        // Ensure rate_per_day is always a string
        if ($this->has('rate_per_day')) {
            $this->merge([
                'rate_per_day' => (string)$this->rate_per_day
            ]);
        }
    }
} 