<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\ServiceRecord;
use App\Models\Employee;

class ServiceRecordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date_from' => ['required', 'date', 'before_or_equal:date_to', 'before_or_equal:today'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
            'designation' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[A-Za-z\s\-\.]+$/',
                'min:3'
            ],
            'status' => [
                'required', 
                'string', 
                'in:permanent,contractual,job_order,probationary,part_time'
            ],
            'payment_frequency' => [
                'required', 
                'string', 
                'in:Daily,Monthly,Annum'
            ],
            'salary' => [
                'required', 
                'string',
                'max:255'
            ],
            'station' => [
                'required', 
                'string', 
                'min:3', 
                'max:255',
                'regex:/^[A-Za-z0-9\s\-\.\,\#]+$/' // Allow numbers and common address characters
            ],
            'service_status' => [
                'required', 
                'string', 
                'in:In Service,Suspension,Not in Service,On Leave'
            ],
            'separation_date' => [
                'nullable', 
                'date', 
                'after_or_equal:date_from',
                'before_or_equal:date_to'
            ],
        ];

        // Add conditional rules based on service status
        if ($this->input('service_status') === 'Not in Service') {
            $rules['separation_date'][] = 'required';
        }

        if ($this->input('service_status') === 'On Leave') {
            $rules['separation_date'] = ['nullable'];
        }

        // Add conditional rules based on status and payment frequency
        if ($this->input('status') === 'job_order') {
            $rules['payment_frequency'][] = 'in:Daily';
            $rules['salary'][] = 'max:5000'; // Maximum daily rate for job order
        }

        if ($this->input('status') === 'part_time') {
            $rules['payment_frequency'][] = 'in:Daily,Monthly';
            $rules['salary'][] = 'max:25000'; // Maximum monthly rate for part-time
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Get the employee
            $employee = Employee::find($this->route('id'));
            if (!$employee) {
                $validator->errors()->add('employee', 'Employee not found.');
                return;
            }

            // Validate against employee's birth date
            $birthDate = Carbon::parse($employee->birth_date);
            $dateFrom = Carbon::parse($this->input('date_from'));
            if ($dateFrom->diffInYears($birthDate) < 18) {
                $validator->errors()->add('date_from', 'Employee must be at least 18 years old at the start of service.');
            }

            // Validate service period
            $dateFrom = Carbon::parse($this->input('date_from'));
            $dateTo = Carbon::parse($this->input('date_to'));
            $servicePeriod = $dateFrom->diffInDays($dateTo);

            // Maximum service period validation based on status
            $status = $this->input('status');
            $maxPeriod = 3650; // Default 10 years

            if ($status === 'job_order') {
                $maxPeriod = 180; // 6 months for job order
            } elseif ($status === 'probationary') {
                $maxPeriod = 180; // 6 months probationary
            } elseif ($status === 'part_time') {
                $maxPeriod = 365; // 1 year for part-time
            }

            if ($servicePeriod > $maxPeriod) {
                $validator->errors()->add('date_to', "Service period cannot exceed " . ($maxPeriod/30) . " months for {$status} status.");
            }

            // Minimum service period validation
            if ($servicePeriod < 1) {
                $validator->errors()->add('date_to', 'Service period must be at least 1 day.');
            }

            // Validate suspension period
            if ($this->input('service_status') === 'Suspension') {
                if ($servicePeriod > 90) {
                    $validator->errors()->add('date_to', 'Suspension period cannot exceed 90 days.');
                }
            }

            // Validate leave period
            if ($this->input('service_status') === 'On Leave') {
                if ($servicePeriod > 30) {
                    $validator->errors()->add('date_to', 'Leave period cannot exceed 30 days.');
                }
            }

            // Validate gaps between records
            $existingRecords = ServiceRecord::where('employee_id', $employee->id)
                ->where('id', '!=', $this->input('record_id'))
                ->orderBy('date_from')
                ->get();

            foreach ($existingRecords as $record) {
                $recordFrom = Carbon::parse($record->date_from);
                $recordTo = Carbon::parse($record->date_to);

                // Check for overlapping periods
                if ($dateFrom->between($recordFrom, $recordTo) || 
                    $dateTo->between($recordFrom, $recordTo) ||
                    ($dateFrom->lte($recordFrom) && $dateTo->gte($recordTo))) {
                    $validator->errors()->add('date_from', 'Service period overlaps with existing record.');
                    break;
                }

                // Check for gaps
                if ($dateFrom->diffInDays($recordTo) > 1) {
                    $validator->errors()->add('date_from', 'There is a gap of more than 1 day with existing records.');
                    break;
                }
            }

            // Validate salary against designation and status
            $salary = $this->input('salary');
            $paymentFrequency = $this->input('payment_frequency');
            $status = $this->input('status');

            // Convert salary to annual for comparison
            $annualSalary = $salary;
            if ($paymentFrequency === 'Monthly') {
                $annualSalary = $salary * 12;
            } elseif ($paymentFrequency === 'Daily') {
                $annualSalary = $salary * 365;
            }

            // Salary range validation based on status
            switch ($status) {
                case 'regularPermanent':
                    if ($annualSalary < 120000) {
                        $validator->errors()->add('salary', 'Salary for Regular Permanent position is below minimum threshold (₱120,000/year).');
                    }
                    if ($annualSalary > 1000000) {
                        $validator->errors()->add('salary', 'Salary for Regular Permanent position exceeds maximum threshold (₱1,000,000/year).');
                    }
                    break;

                case 'contractual':
                    if ($annualSalary < 96000) {
                        $validator->errors()->add('salary', 'Salary for Contractual position is below minimum threshold (₱96,000/year).');
                    }
                    if ($annualSalary > 800000) {
                        $validator->errors()->add('salary', 'Salary for Contractual position exceeds maximum threshold (₱800,000/year).');
                    }
                    break;

                case 'job_order':
                    if ($paymentFrequency !== 'Daily') {
                        $validator->errors()->add('payment_frequency', 'Job Order positions must be paid on a daily basis.');
                    }
                    if ($salary > 5000) {
                        $validator->errors()->add('salary', 'Daily rate for Job Order position cannot exceed ₱5,000/day.');
                    }
                    if ($salary < 500) {
                        $validator->errors()->add('salary', 'Daily rate for Job Order position must be at least ₱500/day.');
                    }
                    break;

                case 'temporary':
                    if ($annualSalary < 84000) {
                        $validator->errors()->add('salary', 'Salary for Temporary position is below minimum threshold (₱84,000/year).');
                    }
                    if ($annualSalary > 600000) {
                        $validator->errors()->add('salary', 'Salary for Temporary position exceeds maximum threshold (₱600,000/year).');
                    }
                    break;

                case 'casual':
                    if ($paymentFrequency === 'Daily') {
                        if ($salary > 800) {
                            $validator->errors()->add('salary', 'Daily rate for Casual position cannot exceed ₱800/day.');
                        }
                        if ($salary < 400) {
                            $validator->errors()->add('salary', 'Daily rate for Casual position must be at least ₱400/day.');
                        }
                    } else {
                        // Convert to annual for monthly/annual validation
                        $annualSalary = $paymentFrequency === 'Monthly' ? $salary * 12 : $salary;
                        if ($annualSalary < 96000) {
                            $validator->errors()->add('salary', 'Annual salary for Casual position must be at least ₱96,000/year.');
                        }
                        if ($annualSalary > 800000) {
                            $validator->errors()->add('salary', 'Annual salary for Casual position cannot exceed ₱800,000/year.');
                        }
                    }
                    break;

                case 'elected':
                    if ($annualSalary < 180000) {
                        $validator->errors()->add('salary', 'Salary for Elected position is below minimum threshold (₱180,000/year).');
                    }
                    if ($annualSalary > 1500000) {
                        $validator->errors()->add('salary', 'Salary for Elected position exceeds maximum threshold (₱1,500,000/year).');
                    }
                    break;

                case 'provisional':
                    if ($annualSalary < 90000) {
                        $validator->errors()->add('salary', 'Salary for Provisional position is below minimum threshold (₱90,000/year).');
                    }
                    if ($annualSalary > 700000) {
                        $validator->errors()->add('salary', 'Salary for Provisional position exceeds maximum threshold (₱700,000/year).');
                    }
                    break;

                case 'coterminous':
                    if ($annualSalary < 150000) {
                        $validator->errors()->add('salary', 'Salary for Coterminous position is below minimum threshold (₱150,000/year).');
                    }
                    if ($annualSalary > 1200000) {
                        $validator->errors()->add('salary', 'Salary for Coterminous position exceeds maximum threshold (₱1,200,000/year).');
                    }
                    break;

                case 'coterminousTemporary':
                    if ($annualSalary < 120000) {
                        $validator->errors()->add('salary', 'Salary for Coterminous-Temporary position is below minimum threshold (₱120,000/year).');
                    }
                    if ($annualSalary > 900000) {
                        $validator->errors()->add('salary', 'Salary for Coterminous-Temporary position exceeds maximum threshold (₱900,000/year).');
                    }
                    break;

                case 'substitute':
                    if ($paymentFrequency === 'Daily') {
                        if ($salary > 1000) {
                            $validator->errors()->add('salary', 'Daily rate for Substitute position cannot exceed ₱1,000/day.');
                        }
                        if ($salary < 600) {
                            $validator->errors()->add('salary', 'Daily rate for Substitute position must be at least ₱600/day.');
                        }
                    } else {
                        // Convert to annual for monthly/annual validation
                        $annualSalary = $paymentFrequency === 'Monthly' ? $salary * 12 : $salary;
                        if ($annualSalary < 120000) {
                            $validator->errors()->add('salary', 'Annual salary for Substitute position must be at least ₱120,000/year.');
                        }
                        if ($annualSalary > 900000) {
                            $validator->errors()->add('salary', 'Annual salary for Substitute position cannot exceed ₱900,000/year.');
                        }
                    }
                    break;
            }

            // Additional payment frequency validations
            if (in_array($status, ['regularPermanent', 'contractual', 'temporary', 'provisional', 'coterminous', 'coterminousTemporary', 'casual', 'substitute'])) {
                if (!in_array($paymentFrequency, ['Monthly', 'Annum'])) {
                    $validator->errors()->add('payment_frequency', 'This position type must be paid on a monthly or annual basis.');
                }
            }

            // Validate designation format and length
            $designation = $this->input('designation');
            if (strlen($designation) < 3) {
                $validator->errors()->add('designation', 'Designation must be at least 3 characters long.');
            }
            if (strlen($designation) > 255) {
                $validator->errors()->add('designation', 'Designation cannot exceed 255 characters.');
            }

            // Validate station format
            $station = $this->input('station');
            if (!preg_match('/^[A-Za-z0-9\s\-\.\,\#]+$/', $station)) {
                $validator->errors()->add('station', 'Station can only contain letters, numbers, spaces, and common address characters (.,-,#).');
            }
        });
    }

    public function messages()
    {
        return [
            'date_from.required' => 'The start date is required.',
            'date_from.before_or_equal' => 'The start date must be before or equal to the end date and cannot be in the future.',
            'date_to.required' => 'The end date is required.',
            'date_to.after_or_equal' => 'The end date must be after or equal to the start date.',
            'designation.required' => 'The designation is required.',
            'designation.regex' => 'The designation can only contain letters, spaces, hyphens, and periods.',
            'designation.min' => 'The designation must be at least 3 characters long.',
            'status.required' => 'The status is required.',
            'status.in' => 'The selected status is invalid.',
            'payment_frequency.required' => 'The payment frequency is required.',
            'payment_frequency.in' => 'The selected payment frequency is invalid.',
            'salary.required' => 'The salary is required.',
            'salary.string' => 'The salary must be a string.',
            'salary.max' => 'The salary exceeds the maximum allowed amount.',
            'station.required' => 'The station is required.',
            'station.min' => 'The station must be at least 3 characters.',
            'station.regex' => 'The station can only contain letters, numbers, spaces, and common address characters (.,-,#).',
            'service_status.required' => 'The service status is required.',
            'service_status.in' => 'The selected service status is invalid.',
            'separation_date.required' => 'The separation date is required when service status is "Not in Service".',
            'separation_date.after_or_equal' => 'The separation date must be after or equal to the start date.',
            'separation_date.before_or_equal' => 'The separation date must be before or equal to the end date.',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * @return void
     */
    protected function prepareForValidation()
    {
        // Ensure salary is always a string to prevent PostgreSQL numeric type issues
        if ($this->has('salary')) {
            $this->merge([
                'salary' => (string)$this->salary
            ]);
        }
    }
} 