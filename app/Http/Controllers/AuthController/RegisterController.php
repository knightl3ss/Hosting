<?php

namespace App\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'birthday' => 'required|date|before:today',
            'status' => 'required|in:Active,Pending,Blocked',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_state' => 'required|string|max:255',
            'address_postal_code' => 'required|string|max:10',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username',
            'role' => 'required|in:Admin',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'gender' => 'required|string|in:male,female,other',
            'extension_name' => 'nullable|string|max:10',
            'employee_id' => 'required|string|unique:users,employee_id|max:20',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validator->addRules([
                'profile_picture' => 'image|mimes:jpeg,png,gif|max:5120', // 5MB max
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $filename = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
            $profilePicturePath = 'storage/' . $path;

            Log::info('Profile Picture Upload Details', [
                'original_name' => $profilePicture->getClientOriginalName(),
                'mime_type' => $profilePicture->getMimeType(),
                'size' => $profilePicture->getSize(),
            ]);

            Log::info('Profile picture uploaded successfully', [
                'new_path' => $profilePicturePath
            ]);
        }

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'age' => $request->input('age'),
            'birthday' => $request->input('birthday'),
            'status' => $request->input('status'),
            'address_street' => $request->input('address_street'),
            'address_city' => $request->input('address_city'),
            'address_state' => $request->input('address_state'),
            'address_postal_code' => $request->input('address_postal_code'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
            'phone_number' => $request->input('phone_number'),
            'gender' => $request->input('gender'),
            'extension_name' => $request->input('extension_name'),
            'employee_id' => $request->input('employee_id'),
            'profile_picture' => $profilePicturePath ?? 'default-profile.png',
        ]);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status
        ]);

        return redirect('/login_page')->with('success', 'Registration successful! Please log in.');
    }

    public function registerModal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'birthday' => 'required|date|before:today',
            'status' => 'required|in:Active,Pending,Blocked',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_state' => 'required|string|max:255',
            'address_postal_code' => 'required|string|max:10',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|alpha_dash|max:255|unique:users,username',
            'role' => 'required|in:Admin',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'gender' => 'required|string|in:male,female,other',
            'extension_name' => 'nullable|string|max:10',
            'employee_id' => 'required|string|unique:users,employee_id|max:20',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validator->addRules([
                'profile_picture' => 'image|mimes:jpeg,png,gif|max:5120', // 5MB max
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $filename = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
            $profilePicturePath = 'storage/' . $path;

            Log::info('Profile Picture Upload Details', [
                'original_name' => $profilePicture->getClientOriginalName(),
                'mime_type' => $profilePicture->getMimeType(),
                'size' => $profilePicture->getSize(),
            ]);

            Log::info('Profile picture uploaded successfully', [
                'new_path' => $profilePicturePath
            ]);
        }

        try {
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'extension_name' => $request->input('extension_name'),
                'age' => $request->input('age'),
                'birthday' => $request->input('birthday'),
                'status' => $request->input('status'),
                'gender' => $request->input('gender'),
                'address_street' => $request->input('address_street'),
                'address_city' => $request->input('address_city'),
                'address_state' => $request->input('address_state'),
                'address_postal_code' => $request->input('address_postal_code'),
                'email' => $request->input('email'),
                'username' => $request->input('username'),
                'phone_number' => $request->input('phone_number'),
                'role' => $request->input('role'),
                'password' => Hash::make($request->input('password')),
                'employee_id' => $request->input('employee_id'),
                'profile_picture' => $profilePicturePath ?? 'default-profile.png',
            ]);

            Log::info('New admin account created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return redirect()->route('account_list')->with('success', 'New admin account created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating admin account: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating account: ' . $e->getMessage())
                ->withInput();
        }
    }

    // AJAX uniqueness check for username, email, or employee_id
    public function checkUnique(Request $request)
    {
        $type = $request->input('type');
        $value = $request->input('value');
        $exists = false;
        
        // Check in users table
        if ($type === 'username') {
            $exists = \App\Models\User::where('username', $value)->exists();
        } elseif ($type === 'email') {
            $exists = \App\Models\User::where('email', $value)->exists();
        } elseif ($type === 'employee_id') {
            // Check in both users and appointments tables
            $exists = \App\Models\User::where('employee_id', $value)->exists();
            
            // If not found in users, check appointments
            if (!$exists) {
                $exists = \App\Models\AppointmentModel\Appointment::where('employee_id', $value)
                    ->where('is_active', true)
                    ->exists();
            }
        } elseif ($type === 'item_no') {
            // Check in appointments table for item_no
            $exists = \App\Models\AppointmentModel\Appointment::where('item_no', $value)
                ->where('is_active', true)
                ->exists();
        }
        
        return response()->json(['exists' => $exists]);
    }

    // Display a single account by ID
    public function viewAccount($id)
    {
        $user = User::findOrFail($id);
        return view('Pages.Account.account_view', compact('user'));
    }

    // Check if a user has any service records
    private function userHasServiceRecords($userId)
    {
        return DB::table('service_records')
            ->where('created_by', $userId)
            ->exists();
    }

    // Display a list of all accounts
    public function accountList()
    {
        $users = User::all();
        
        // Check which users have service records
        $usersWithRecords = [];
        foreach ($users as $user) {
            $usersWithRecords[$user->id] = $this->userHasServiceRecords($user->id);
        }
        
        return view('Pages.Account.account_list', compact('users', 'usersWithRecords'));
    }

    // Show the form for editing a specific account
    public function editAccount($id)
    {
        $user = User::findOrFail($id);
        return view('Pages.Account.account_edit', compact('user'));
    }

    // Delete a specific account by ID
    public function deleteAccount($id)
    {
        $user = User::findOrFail($id);
        
        // Check if user has service records
        if ($this->userHasServiceRecords($user->id)) {
            return redirect()->route('account_list')->with('error', 'This account cannot be deleted because it has associated service records. Please block the account instead.');
        }
        
        $user->delete();
        return redirect()->route('account_list')->with('success', 'Account deleted successfully.');
    }
    
    // Block a specific account by ID
    public function blockAccount($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Blocked';
        $user->save();
        
        return redirect()->route('account_list')->with('success', 'Account has been blocked successfully.');
    }
    
    // Unblock a specific account by ID
    public function unblockAccount($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Active';
        $user->save();
        
        return redirect()->route('account_list')->with('success', 'Account has been unblocked successfully.');
    }

    // Update a specific account by ID
    public function updateAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Enhanced debug logging
        \Log::info('UpdateAccount Detailed Debug', [
            'user_id' => $user->id,
            'current_values' => [
                'email' => $user->email,
                'username' => $user->username,
                'employee_id' => $user->employee_id,
            ],
            'submitted_values' => [
                'email' => $request->input('email'),
                'username' => $request->input('username'),
                'employee_id' => $request->input('employee_id'),
            ],
            'form_data' => $request->all(),
            'read_only_checks' => [
                'email_readonly' => $request->has('email_readonly') ? $request->input('email_readonly') : 'not_set',
                'username_readonly' => $request->has('username_readonly') ? $request->input('username_readonly') : 'not_set',
                'employee_id_readonly' => $request->has('employee_id_readonly') ? $request->input('employee_id_readonly') : 'not_set',
            ]
        ]);

        // Create a new request with the existing user data for fields that are readonly
        $modifiedRequest = $request->all();
        
        // Check each unique field and force it to use existing value if readonly
        if ($request->has('email_readonly') && $request->input('email_readonly') === 'true') {
            $modifiedRequest['email'] = $user->email;
        }
        
        if ($request->has('username_readonly') && $request->input('username_readonly') === 'true') {
            $modifiedRequest['username'] = $user->username;
        }
        
        if ($request->has('employee_id_readonly') && $request->input('employee_id_readonly') === 'true') {
            $modifiedRequest['employee_id'] = $user->employee_id;
        }
        
        // Create a new request with our modified data
        $cleanRequest = new \Illuminate\Http\Request($modifiedRequest);

        // Define base validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'birthday' => 'required|date|before:today',
            'status' => 'required|in:Active,Pending,Blocked',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_state' => 'required|string|max:255',
            'address_postal_code' => 'required|string|max:10',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'gender' => 'required|string|in:male,female,other',
            'extension_name' => 'nullable|string|max:10',
            'role' => 'required|in:Admin',
        ];

        // Skip uniqueness validation completely for fields that match the current user values
        if ($cleanRequest->input('email') === $user->email) {
            $rules['email'] = 'required|email|max:100';
        } else {
            $rules['email'] = 'required|email|max:100|unique:users,email,' . $user->id;
        }

        if ($cleanRequest->input('username') === $user->username) {
            $rules['username'] = 'required|string|alpha_dash|max:255';
        } else {
            $rules['username'] = 'required|string|alpha_dash|max:255|unique:users,username,' . $user->id;
        }

        if ($cleanRequest->input('employee_id') === $user->employee_id) {
            $rules['employee_id'] = 'required|string|max:20';
        } else {
            $rules['employee_id'] = 'required|string|max:20|unique:users,employee_id,' . $user->id;
        }

        // Log final validation rules and data
        \Log::info('Final validation data', [
            'rules' => $rules,
            'modified_request' => $modifiedRequest
        ]);

        try {
            $validated = \Illuminate\Support\Facades\Validator::make($modifiedRequest, $rules)->validate();
            
            $user->fill($validated);
            $user->save();
            
            return redirect()->route('account_list')->with('success', 'Account updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
            ]);
            
            throw $e;
        }
    }
}
