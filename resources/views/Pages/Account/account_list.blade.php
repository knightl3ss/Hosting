@extends('layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Admin Accounts</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registrationModal">
                    <i class="fas fa-plus"></i> Add Admin
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-hover" id="adminTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Employee ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->role }}</td>
                            @php
                                // Define status color mapping
                                $statusColors = [
                                    'active' => ['class' => 'success', 'icon' => 'check-circle'],
                                    'pending' => ['class' => 'warning', 'icon' => 'clock'],
                                    'inactive' => ['class' => 'danger', 'icon' => 'times-circle'],
                                    'suspended' => ['class' => 'secondary', 'icon' => 'ban'],
                                    'archived' => ['class' => 'light text-muted', 'icon' => 'archive']
                                ];
                            @endphp
                            <td>
                                @php
                                    $status = strtolower(ucfirst($user->status));
                                    $statusConfig = $statusColors[$status] ?? $statusColors['inactive'];
                                @endphp
                                <span class="badge bg-{{ $statusConfig['class'] }} d-inline-flex align-items-center gap-2 py-2 px-3 rounded-pill">
                                    <i class="fas fa-{{ $statusConfig['icon'] }} me-1"></i>
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-clock text-muted mb-1" style="font-size: 1.2em;"></i>
                                        <span class="text-center w-100">
                                            {{ $user->last_login_at->diffForHumans() }}
                                            <small class="d-block text-muted">
                                            {{ $user->last_login_at->timezone('Asia/Manila')->format('F j, Y g:ia') }}
                                            </small>
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-ban mr-2"></i>Never logged in
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-info btn-sm view-account-btn"
                                        title="View Details"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewAccountModal"
                                        data-user-id="{{ $user->id }}"
                                        data-first_name="{{ $user->first_name }}"
                                        data-middle_name="{{ $user->middle_name }}"
                                        data-last_name="{{ $user->last_name }}"
                                        data-extension_name="{{ $user->extension_name }}"
                                        data-employee_id="{{ $user->employee_id }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-phone_number="{{ $user->phone_number }}"
                                        data-role="{{ $user->role }}"
                                        data-status="{{ $user->status }}"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="#" class="btn btn-warning btn-sm edit-account-btn" title="Edit Account" data-bs-toggle="modal" data-bs-target="#editAccountModal"
                                        data-user-id="{{ $user->id }}"
                                        data-first_name="{{ $user->first_name }}"
                                        data-middle_name="{{ $user->middle_name }}"
                                        data-last_name="{{ $user->last_name }}"
                                        data-extension_name="{{ $user->extension_name }}"
                                        data-age="{{ $user->age }}"
                                        data-birthday="{{ $user->birthday }}"
                                        data-status="{{ $user->status }}"
                                        data-gender="{{ $user->gender }}"
                                        data-address_street="{{ $user->address_street }}"
                                        data-address_city="{{ $user->address_city }}"
                                        data-address_state="{{ $user->address_state }}"
                                        data-address_postal_code="{{ $user->address_postal_code }}"
                                        data-employee_id="{{ $user->employee_id }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-phone_number="{{ $user->phone_number }}"
                                        data-role="{{ $user->role }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('delete_account', ['id' => $user->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Account">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Separated Registration Modal --}}
@include('Pages.Account.account_modal')

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

<script src="{{ asset('js/Auth/registration.js') }}"></script>
<script src="{{ asset('js/Auth/edit.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-account-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Fill modal fields with data attributes
            document.getElementById('edit_first_name').value = this.dataset.first_name || '';
            document.getElementById('edit_middle_name').value = this.dataset.middle_name || '';
            document.getElementById('edit_last_name').value = this.dataset.last_name || '';
            document.getElementById('edit_extension_name').value = this.dataset.extension_name || '';
            document.getElementById('edit_age').value = this.dataset.age || '';
            document.getElementById('edit_birthday').value = this.dataset.birthday || '';
            document.getElementById('edit_status').value = this.dataset.status || '';
            document.getElementById('edit_gender').value = this.dataset.gender || '';
            document.getElementById('edit_address_street').value = this.dataset.address_street || '';
            document.getElementById('edit_address_city').value = this.dataset.address_city || '';
            document.getElementById('edit_address_state').value = this.dataset.address_state || '';
            document.getElementById('edit_address_postal_code').value = this.dataset.address_postal_code || '';
            document.getElementById('edit_employee_id').value = this.dataset.employee_id || '';
            document.getElementById('edit_username').value = this.dataset.username || '';
            document.getElementById('edit_email').value = this.dataset.email || '';
            document.getElementById('edit_phone_number').value = this.dataset.phone_number || '';
            document.getElementById('edit_role').value = this.dataset.role || '';
            // Update form action with correct user id
            const form = document.getElementById('editAccountForm');
            form.action = `/update_account/${this.dataset.userId}`;
            // Clear password fields
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
        });
    });
});
</script>
@endsection
