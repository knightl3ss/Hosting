<?php

namespace App\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

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
            'email' => 'required|email|unique:users,email',
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
            'email' => 'required|email|unique:users,email',
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
        if ($type === 'username') {
            $exists = \App\Models\User::where('username', $value)->exists();
        } elseif ($type === 'email') {
            $exists = \App\Models\User::where('email', $value)->exists();
        } elseif ($type === 'employee_id') {
            $exists = \App\Models\User::where('employee_id', $value)->exists();
        }
        return response()->json(['exists' => $exists]);
    }

    // Display a single account by ID
    public function viewAccount($id)
    {
        $user = User::findOrFail($id);
        return view('Pages.Account.account_view', compact('user'));
    }

    // Display a list of all accounts
    public function accountList()
    {
        $users = User::all();
        return view('Pages.Account.account_list', compact('users'));
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
        $user->delete();
        return redirect()->route('account_list')->with('success', 'Account deleted successfully.');
    }

    // Update a specific account by ID
    public function updateAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Debug logging: show current and incoming values
        \Log::info('UpdateAccount Debug', [
            'user_id' => $user->id,
            'current_email' => $user->email,
            'current_username' => $user->username,
            'current_employee_id' => $user->employee_id,
            'submitted_email' => $request->input('email'),
            'submitted_username' => $request->input('username'),
            'submitted_employee_id' => $request->input('employee_id'),
        ]);

        $validated = $request->validate([
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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|alpha_dash|max:255|unique:users,username,' . $user->id,
            'role' => 'required|in:Admin',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'gender' => 'required|string|in:male,female,other',
            'extension_name' => 'nullable|string|max:10',
            'employee_id' => 'required|string|unique:users,employee_id,' . $user->id . '|max:20',
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->route('account_list')->with('success', 'Account updated successfully.');
    }
}
