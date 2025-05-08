<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    public function showProfile()
{
    $user = Auth::user();
    return view('Pages.Additional.profile.Profile', [
        'first_name' => $user->first_name,
        'middle_name' => $user->middle_name,
        'last_name' => $user->last_name,
        'extension_name' => $user->extension_name,
        'age' => $user->age,
        'birthday' => $user->birthday,
        'email' => $user->email,
        'phone_number' => $user->phone_number,
        'role' => $user->role,
        'status' => $user->status,
        'address_street' => $user->address_street,
        'address_city' => $user->address_city,
        'address_state' => $user->address_state,
        'address_postal_code' => $user->address_postal_code,
        'gender' => $user->gender,
        'employee_id' => $user->employee_id,
        'profile_picture' => $user->profile_picture ?? 'default-profile.png',
    ]);
}

public function updateAccount(Request $request, $id = null)
{
    // If no ID is provided, use the authenticated user's ID
    $id = $id ?? Auth::id();
    
    // Find the user or fail (404 if not found)
    $user = User::findOrFail($id);

    // Validate the request data
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'age' => 'required|integer|min:18|max:100',
        'birthday' => 'required|date|before:today',
        'address_street' => 'required|string|max:255',
        'address_city' => 'required|string|max:255',
        'address_state' => 'required|string|max:255',
        'address_postal_code' => 'required|string|max:10',
        'email' => 'required|email|unique:users,email,'.$user->id,
        'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
        'gender' => 'required|string|in:male,female,other',
        'extension_name' => 'nullable|string|max:10',
    ]);

    // Add profile picture validation
    if ($request->hasFile('profile_picture')) {
        $validator->addRules([
            'profile_picture' => 'image|mimes:jpeg,png,gif|max:5120', // 5MB max
        ]);
    }

    // Check if validation fails
    if ($validator->fails()) {
        Log::error('Profile update validation failed', [
            'errors' => $validator->errors(),
            'input' => $request->except(['password', 'password_confirmation'])
        ]);
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        // Handle profile picture upload
        $profilePicturePath = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $filename = uniqid() . '.' . $profilePicture->getClientOriginalExtension();
            $path = $profilePicture->storeAs('profile_pictures', $filename, 'public');
            $profilePicturePath = 'storage/' . $path;

            // Delete old profile picture if it's not the default
            if ($user->profile_picture && $user->profile_picture !== 'default-profile.png') {
                $oldPath = str_replace('storage/', '', $user->profile_picture);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        // Prepare update data
        $updateData = [
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'extension_name' => $request->input('extension_name'),
            'gender' => $request->input('gender'),
            'age' => $request->input('age'),
            'birthday' => $request->input('birthday'),
            'address_street' => $request->input('address_street'),
            'address_city' => $request->input('address_city'),
            'address_state' => $request->input('address_state'),
            'address_postal_code' => $request->input('address_postal_code'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'employee_id' => $request->input('employee_id'),
            'profile_picture' => $profilePicturePath,
        ];

        // Update the user
        $user->update($updateData);

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    } catch (\Exception $e) {
        Log::error('Profile update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
    }
}

    /**
     * Send OTP to user's email for password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        try {
            $email = $request->input('email');
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Generate a 6-digit OTP
            $otp = sprintf('%06d', mt_rand(1, 999999));
            
            // Store OTP in session with expiry time (10 minutes)
            Session::put('password_reset_otp', [
                'email' => $email,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]);

            // Send email with OTP
            $this->sendOtpEmail($user, $otp);

            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to send OTP'], 500);
        }
    }

    /**
     * Send OTP email to user
     *
     * @param  User  $user
     * @param  string  $otp
     * @return void
     */
    private function sendOtpEmail(User $user, $otp)
    {
        $name = $user->first_name . ' ' . $user->last_name;
        $data = [
            'name' => $name,
            'otp' => $otp
        ];

        Mail::send('emails.password_reset_otp', $data, function($message) use ($user) {
            $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                    ->subject('Password Reset OTP');
        });
    }

    /**
     * Verify OTP for password reset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            $email = $request->input('email');
            $inputOtp = $request->input('otp');
            
            // Get OTP data from session
            $otpData = Session::get('password_reset_otp');
            
            // Validate OTP data
            if (!$otpData || $otpData['email'] !== $email) {
                return response()->json(['message' => 'Invalid OTP request'], 400);
            }
            
            // Check if OTP is expired
            if (now()->isAfter($otpData['expires_at'])) {
                Session::forget('password_reset_otp');
                return response()->json(['message' => 'OTP has expired'], 400);
            }
            
            // Validate OTP
            if ($otpData['otp'] !== $inputOtp) {
                return response()->json(['message' => 'Invalid OTP'], 400);
            }
            
            // Generate and store a reset token
            $resetToken = Str::random(60);
            Session::put('password_reset_token', [
                'email' => $email,
                'token' => $resetToken,
                'expires_at' => now()->addMinutes(30),
            ]);
            
            return response()->json(['message' => 'OTP verified successfully', 'token' => $resetToken]);
        } catch (\Exception $e) {
            Log::error('Failed to verify OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to verify OTP'], 500);
        }
    }

    /**
     * Reset user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            
            // Validate the reset token
            $resetData = Session::get('password_reset_token');
            if (!$resetData || $resetData['email'] !== $email) {
                return response()->json(['message' => 'Invalid reset request'], 400);
            }
            
            // Check if token is expired
            if (now()->isAfter($resetData['expires_at'])) {
                Session::forget('password_reset_token');
                return response()->json(['message' => 'Reset session has expired'], 400);
            }
            
            // Validate password
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 400);
            }
            
            // Update user password
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            
            $user->password = Hash::make($request->input('password'));
            $user->save();
            
            // Clear session data
            Session::forget(['password_reset_otp', 'password_reset_token']);
            
            return response()->json(['message' => 'Password reset successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to reset password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to reset password'], 500);
        }
    }
}

// return view('Pages.Plantilla.index');