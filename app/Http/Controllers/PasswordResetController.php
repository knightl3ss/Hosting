<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\AuthService;

class PasswordResetController extends Controller
{
    /**
     * Send OTP to user's email
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $email = $request->email;
        
        // Find user with encrypted email
        $user = AuthService::findUserByEmail($email);
        
        if (!$user) {
            return response()->json(['message' => 'No account found with this email address'], 404);
        }
        
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Store OTP in password_resets table with a hash of the email to avoid encryption issues
        $emailHash = md5($email); // Use a hash as the lookup key
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $emailHash],
            [
                'token' => $otp,
                'created_at' => Carbon::now()
            ]
        );
        
        // Send email with OTP
        try {
            Mail::send('emails.password_reset_otp', ['otp' => $otp], function($message) use ($email) {
                $message->to($email)
                        ->subject('Your Password Reset Code');
            });
            
            return response()->json(['message' => 'Verification code sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send verification code: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);
        
        $email = $request->email;
        $otp = $request->otp;
        $emailHash = md5($email); // Use the same hash as in sendOTP
        
        // Check if OTP is valid and not expired (10 minutes validity)
        $reset = DB::table('password_resets')
                   ->where('email', $emailHash)
                   ->where('token', $otp)
                   ->where('created_at', '>', Carbon::now()->subMinutes(10))
                   ->first();
        
        if (!$reset) {
            return response()->json(['message' => 'Invalid or expired verification code'], 400);
        }
        
        return response()->json(['message' => 'Verification successful'], 200);
    }
    
    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $email = $request->email;
        $emailHash = md5($email); // Use the same hash as in sendOTP and verifyOTP
        
        // Find user with encrypted email
        $user = AuthService::findUserByEmail($email);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Delete password reset tokens
        DB::table('password_resets')->where('email', $emailHash)->delete();
        
        return response()->json(['message' => 'Password has been reset successfully'], 200);
    }
}