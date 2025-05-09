@extends('Layout.app')

@php $typeLabels = config('appointment_types'); @endphp

@push('styles')
<link href="{{ asset('css/servicerecord/purpose.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Service Records</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Record Purpose</li>
                </ol>
            </nav>
            <h1 class="text-primary">Record Purpose</h1>
            <p class="text-muted">Specify the purpose for requesting a service record</p>
        </div>
    </div>
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card purpose-card">
                <div class="card-header purpose-header py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-file-alt me-2"></i>Service Record Purpose
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Employee Information -->
                    <div class="employee-info mb-4">
                        <div class="employee-name">{{ $employee->name }}</div>
                        <div class="employee-details mt-2">
                            <div><strong>Employee ID:</strong> {{ $employee->employee_id }}</div>
                            <div><strong>Position:</strong> {{ $employee->position ?? 'Not specified' }}</div>
                            <div><strong>Office:</strong> {{ $employee->office_assignment ?? 'Not assigned' }}</div>
                            <div><strong>Employment Status:</strong> {{ $typeLabels[$employee->appointment_type] ?? ucfirst($employee->appointment_type ?? 'Unknown') }}</div>
                        </div>
                    </div>
                    
                    <form action="{{ route('record_purpose.store') }}" method="POST" class="purpose-form">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="mb-4">
                            <label for="purpose_type" class="form-label">Select Purpose</label>
                            <select class="form-select" id="purpose_type" name="purpose_type">
                                <option value="" selected disabled>-- Select a purpose --</option>
                                <option value="promotion">Promotion</option>
                                <option value="transfer">Transfer</option>
                                <option value="retirement">Retirement</option>
                                <option value="loan">Loan Application</option>
                                <option value="other">Other Purpose</option>
                            </select>
                            <div class="form-text mt-2">
                                <small>Please select the purpose for requesting your service record.</small>
                            </div>
                        </div>
                        
                        <div class="mb-4 other-purpose-container d-none">
                            <label for="other_purpose" class="form-label">Specify Other Purpose</label>
                            <input type="text" class="form-control" id="other_purpose" name="other_purpose" placeholder="Please specify the purpose">
                        </div>
                        
                        <div class="mb-4">
                            <label for="purpose_details" class="form-label">Additional Details (Optional)</label>
                            <textarea class="form-control" id="purpose_details" name="purpose_details" rows="3" placeholder="Provide any additional details about your request"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="requested_date" class="form-label">Date Needed</label>
                            <input type="date" class="form-control" id="requested_date" name="requested_date" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('service_records') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Service Records
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Submit Purpose
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card purpose-card">
                <div class="card-header purpose-header py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Information
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Why do we need this information?</h6>
                    <p>Recording the purpose of your service record request helps us:</p>
                    <ul>
                        <li>Process your request more efficiently</li>
                        <li>Prepare the appropriate documentation</li>
                        <li>Track and manage service record requests</li>
                        <li>Comply with record-keeping requirements</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> Your service record will be prepared according to the purpose specified. Please ensure the information provided is accurate.
                    </div>
                    
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Processing Time</h6>
                        <p>Service records are typically processed within 3-5 working days from the date of request submission.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Purpose History Table -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Purpose History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date Requested</th>
                                <th>Purpose</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purposeHistory as $purpose)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($purpose->requested_date)->format('F d, Y') }}</td>
                                    <td>{{ $purpose->purpose }}</td>
                                    <td>{{ $purpose->purpose_details ?? '-' }}</td>
                                    <td>{{ $purpose->status ?? 'Pending' }}</td>
                                    <td>
                                        @if($purpose->status !== 'Completed')
                                            <form action="{{ route('record_purpose.complete', $purpose->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Mark as Completed</button>
                                            </form>
                                        @else
                                            <span class="badge bg-success">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No purpose history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Purpose History Table -->
</div>
@endsection

@push('scripts')
@endpush