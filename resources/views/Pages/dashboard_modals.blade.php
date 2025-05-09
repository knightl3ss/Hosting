{{-- Modals for Statistics Cards --}}
<!-- Male Employees Modal -->
<div class="modal fade" id="maleEmployeesModal" tabindex="-1" aria-labelledby="maleEmployeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maleEmployeesModalLabel">
                    <i class="fas fa-male text-primary me-2"></i>Male Employees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Appointment Type</th>
                                <th>Office Assignment</th>
                                <th>Service Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maleEmployees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td>{{ $employee->office_assignment }}</td>
                                <td>
                                    @php
                                        $latestServiceRecord = DB::table('service_records')
                                            ->where('employee_id', $employee->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first();
                                        $status = $latestServiceRecord ? $latestServiceRecord->service_status : 'Not specified';
                                        $statusClass = '';
                                        if($status == 'In Service') $statusClass = 'bg-success';
                                        elseif($status == 'Suspension') $statusClass = 'bg-warning text-dark';
                                        elseif($status == 'Not in Service') $statusClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Female Employees Modal -->
<div class="modal fade" id="femaleEmployeesModal" tabindex="-1" aria-labelledby="femaleEmployeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="femaleEmployeesModalLabel">
                    <i class="fas fa-female text-danger me-2"></i>Female Employees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Appointment Type</th>
                                <th>Office Assignment</th>
                                <th>Service Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($femaleEmployees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td>{{ $employee->office_assignment }}</td>
                                <td>
                                    @php
                                        $latestServiceRecord = DB::table('service_records')
                                            ->where('employee_id', $employee->id)
                                            ->orderBy('updated_at', 'desc')
                                            ->first();
                                        $status = $latestServiceRecord ? $latestServiceRecord->service_status : 'Not specified';
                                        $statusClass = '';
                                        if($status == 'In Service') $statusClass = 'bg-success';
                                        elseif($status == 'Suspension') $statusClass = 'bg-warning text-dark';
                                        elseif($status == 'Not in Service') $statusClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- In Service Employees Modal -->
<div class="modal fade" id="inServiceEmployeesModal" tabindex="-1" aria-labelledby="inServiceEmployeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inServiceEmployeesModalLabel">
                    <i class="fas fa-user-check text-success me-2"></i>In Service Employees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Appointment Type</th>
                                <th>Office Assignment</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inServiceEmployees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td>{{ $employee->office_assignment }}</td>
                                <td>
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suspension Employees Modal -->
<div class="modal fade" id="suspensionEmployeesModal" tabindex="-1" aria-labelledby="suspensionEmployeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suspensionEmployeesModalLabel">
                    <i class="fas fa-user-clock text-warning me-2"></i>Employees on Suspension
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Appointment Type</th>
                                <th>Office Assignment</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suspensionEmployees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td>{{ $employee->office_assignment }}</td>
                                <td>
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Not in Service Employees Modal -->
<div class="modal fade" id="notInServiceEmployeesModal" tabindex="-1" aria-labelledby="notInServiceEmployeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notInServiceEmployeesModalLabel">
                    <i class="fas fa-user-times text-danger me-2"></i>Not in Service Employees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Appointment Type</th>
                                <th>Office Assignment</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notInServiceEmployees as $employee)
                            <tr>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td>{{ $employee->office_assignment }}</td>
                                <td>
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 10+ Years Service Modal -->
<div class="modal fade" id="modal-10years" tabindex="-1" aria-labelledby="modal10YearsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center w-100" id="modal10YearsLabel">
                    <i class="fas fa-users text-primary me-2"></i>Employees with 10+ Years of Service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Position</th>
                                <th class="text-center">Appointment Type</th>
                                <th class="text-center">Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceGroups['10 Years+'] as $employee)
                            <tr>
                                <td class="text-center">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td class="text-center">{{ $employee->position }}</td>
                                <td class="text-center">
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5-9 Years Service Modal -->
<div class="modal fade" id="modal-5to9years" tabindex="-1" aria-labelledby="modal5to9YearsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center w-100" id="modal5to9YearsLabel">
                    <i class="fas fa-user-friends text-success me-2"></i>Employees with 5-9 Years of Service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Position</th>
                                <th class="text-center">Appointment Type</th>
                                <th class="text-center">Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceGroups['5-9 Years'] as $employee)
                            <tr>
                                <td class="text-center">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td class="text-center">{{ $employee->position }}</td>
                                <td class="text-center">
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Below 5 Years Service Modal -->
<div class="modal fade" id="modal-below5years" tabindex="-1" aria-labelledby="modalBelow5YearsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center w-100" id="modalBelow5YearsLabel">
                    <i class="fas fa-user-plus text-info me-2"></i>Employees with Below 5 Years of Service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Position</th>
                                <th class="text-center">Appointment Type</th>
                                <th class="text-center">Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceGroups['Below 5 Years'] as $employee)
                            <tr>
                                <td class="text-center">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td class="text-center">{{ $employee->position }}</td>
                                <td class="text-center">
                                    <span class="badge text-info">
                                        {{ $appointmentTypes[$employee->appointment_type] ?? $employee->appointment_type }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $employee->gender == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ ucfirst($employee->gender) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
