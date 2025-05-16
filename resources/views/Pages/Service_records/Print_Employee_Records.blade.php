<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/servicerecord/print.css') }}">  
</head>
<body>
    
@php
    $typeLabels = config('appointment_types');
    $salaryPer = 'Annum';
    $salaryDivisor = 1;
    if (count($serviceRecords) > 0) {
        $latest = $serviceRecords->last();
        if (isset($latest->payment_frequency)) {
            if (strtolower($latest->payment_frequency) === 'monthly') {
                $salaryPer = 'Monthly';
                $salaryDivisor = 12;
            } elseif (strtolower($latest->payment_frequency) === 'daily') {
                $salaryPer = 'Daily';
                $salaryDivisor = 365;
            }
        }
    }
@endphp

<div class="card-body p-0">
<div class="action-buttons no-print">
    <button class="btn btn-print" onclick="window.print()">Print this page</button>
    <button onclick="history.back()" class="btn btn-return">Return</button>
</div>
              <div class="table-responsive">
                <div class="header-container">
                    <div class="header-container">
                        <div class="logo-left">
                            <img src="{{ asset('images/Municipal Logo of Magallanes.png') }}" alt="Bitaug Logo" class="header-logo">
                        </div>
                        <div class="text-header">
                            <p>Republic of the Philippines</p>
                            <p>PROVINCE OF AGUSAN DEL NORTE</p>
                            <p class="fw-bold">Municipality of Magallanes</p>
                        </div>
                        <div class="logo-right">
                            <img src="{{ asset('images/bitaug.jpg') }}" alt="Municipal Logo of Magallanes" class="header-logo">
                        </div>
                    </div>
                   
                </div>
                     <h2>SERVICE RECORD</h2>
                    <table class="name-table">
                        <tr>
                            <th>Name:</th> 
                            <td class="border-bottom"colspan="2" stat>{{ $employee->last_name }}</td>
                            <td class="border-bottom"colspan="2" style="width: 22%;">{{ $employee->first_name }}</td>
                            <td class="border-bottom"colspan="2">{{ $employee->middle_name }}</td>
                            <td class="paragraph-1" style="font-size: 16px;">(if married, give also a full maiden name)</td>
                        </tr>
                        <tr>
                            <th></th>
                            <td colspan="2">Surname</td>
                            <td colspan="2">Given Name</td>
                            <td colspan="2">Middle Name</td>
                        </tr>
                        </table>

                        <table class="name-table">
                        <tr>
                            <th>Birth:</th> 
                            <td class="border-bottom" width="25%">{{ \Carbon\Carbon::parse($employee->birthday)->format('F d, Y') }}</td>
                            <td class="border-bottom" width="25%">{{ $employee->location
                             }}</td>
                            <td></td>
                            <td class="paragraph-1" colspan="2" style="font-size: 15px;">Data hermin should be checked from birth or
                            baptismal certificate or some other reliable documents.</td>
                        </tr>
                        <tr>
                            <th></th>
                            <td style="width: 10%;">(Date)</td>
                            <td style="width: 10%;">(Place)</td>
                            <td></td>
                        </tr>
                    </table>
                    <p>This is to certify that above employee named herein- actually rendered services in the Office as
                        shown by the service record below, each line of which is supported by the appointment and other papers actually issued
                        by the Office and approved by the authorities concerned. </p>
                    <table class="table service-record-table">
                        <thead>
                            <tr>
                                <th colspan="2">Service</th>
                                <th colspan="3">Record of Appointment</th>
                                <th colspan="4">Office Entity/Leave Division Absence</th>
                                <th rowspan="2">Separation Date</th>
                               
                            </tr>
                            <tr>
                                <th colspan="2">Inclusive Dates</th>
                                <th>Designation</th>
                                <th>Status Salary</th>
                                <th>Salary Per {{ $salaryPer }}</th>
                                <th>Station Place</th>
                                <th colspan="2">Branch</th>
                                <th>Without Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($serviceRecords) > 0)
                                @foreach($serviceRecords as $record)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($record->date_from)->format('m/d/y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->date_to)->format('m/d/y') }}</td>
                                    <td>{{ $record->designation }}</td>
                                    <td>
                                        @if(isset($record->status))
                                            {{ $typeLabels[$record->status] ?? ucwords(str_replace('_', ' ', $record->status)) }}{{ isset($record->payment_frequency) && !empty($record->payment_frequency) ? '/'.$record->payment_frequency : '' }}
                                        @else
                                            <span class="text-danger">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        ₱{{ isset($record->salary) ? number_format((float)$record->salary, 2) : '0.00' }}
                                    </td>
                                    <td colspan="4" style="border: 1px solid black;">{{ $record->station }}{{ !empty($record->branch) ? ', '.$record->branch : '' }}{{ !empty($record->location) ? ', '.$record->location : '' }}{{ !empty($record->lwop) ? ', '.$record->lwop : '' }}</td>
                                    <td>{{ $record->separation_date}}</td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="11" class="text">No service records found</td>
                            </tr>
                            @endif  
                            <tr>
                                <th colspan="11" class="text">*** Nothing follow ***</th>
                            </tr>
                        </tbody>
                    </table>
                    <p>Issued in compliance with Executive No. 54, dated August 10, 1954, and in accordance with Circular 
                        NO. 58, dated August 10, 1954 of the system.
                    </p>
                    <h5 class="text-date">{{ \Carbon\Carbon::now()->format('F d, Y') }}</h5>
                    
                    <div class="row">
                    <div class="paragraph">
                    <p>Certified Correct:</p>
                    
                    
                    <input type="text" class="name editable-field" value="JESSIE M. RODAS" style="font-size:1.0em; margin-left:185px;">
                    <input type="text" class="position1 editable-field" value="MGADH-|(HRMO)" style="margin-left:185px; margin-top:-10px;">
                    
                    </div>
                    <div class="paragraph">
                    <p>Noted:</p>
                    <input type="text" class="name editable-field" value="CESAR C. CUMBA JR." style="font-size:1.0em; margin-left:200px;">
                    <input type="text" class="position editable-field" value="Municipal Mayor" style="margin-left:200px; margin-top:-10px;">
                    </div>
                    </div>
                    <div class="alternate-signatory-section">
                        <div class="toggle-container no-print">
                            <button id="toggle-alternate-signatory" class="toggle-button">
                                <i class="arrow-icon">&#9660;</i>
                            </button>
                        </div>
                        
                        <!-- Content container - only shown when toggled or when printing if it was toggled -->
                        <div id="alternate-signatory-fields" class="alternate-signatory-fields" style="display: none;">
                            <p class="alternate-signatory-label">For and in the absence of the human resource Officer:</p>
                            <input type="text" class="name editable-field" value="KRIZELLE MAE F. PENASO" style="font-size:1.0em; margin-left:185px;">
                            <input type="text" class="position1 editable-field" value="administrative Officer IV (HRMO II)" style="margin-left:185px; margin-top:-10px;">
                        </div>
                    </div>
            </div>
        </div>
    </div>  
   
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggle-alternate-signatory');
        const fieldsContainer = document.getElementById('alternate-signatory-fields');
        const arrowIcon = toggleButton.querySelector('.arrow-icon');
        
        toggleButton.addEventListener('click', function() {
            // Toggle visibility of the fields
            if (fieldsContainer.style.display === 'none') {
                fieldsContainer.style.display = 'block';
                arrowIcon.classList.add('open');
                fieldsContainer.classList.add('show-in-print'); // Mark to show when printing
            } else {
                fieldsContainer.style.display = 'none';
                arrowIcon.classList.remove('open');
                fieldsContainer.classList.remove('show-in-print'); // Mark to hide when printing
            }
        });
        
        // Check if we need to restore state (e.g., after page refresh)
        if (localStorage.getItem('alternateSignatoryVisible') === 'true') {
            fieldsContainer.style.display = 'block';
            arrowIcon.classList.add('open');
            fieldsContainer.classList.add('show-in-print');
        }
        
        // Save state when changed
        toggleButton.addEventListener('click', function() {
            localStorage.setItem('alternateSignatoryVisible', 
                fieldsContainer.style.display === 'block');
        });
    });
</script>
</html>
