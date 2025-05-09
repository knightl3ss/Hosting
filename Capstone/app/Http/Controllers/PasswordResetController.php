<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Send OTP to user's email
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address',
        ]);
        
        $email = $request->email;
        
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Store OTP in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
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
        
        // Check if OTP is valid and not expired (10 minutes validity)
        $reset = DB::table('password_resets')
                   ->where('email', $email)
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
        
        // Find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Delete password reset tokens
        DB::table('password_resets')->where('email', $email)->delete();
        
        return response()->json(['message' => 'Password has been reset successfully'], 200);
    }
} 