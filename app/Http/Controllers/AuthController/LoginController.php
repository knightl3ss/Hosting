<?php

namespace App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Backend validation matching your JS rules
        $request->validate([
            'email' => [
                'required',
                'email',
                // Block specific domains
                function($attribute, $value, $fail) {
                    $restrictedDomains = ['@test.com', '@blocked.com'];
                    foreach ($restrictedDomains as $domain) {
                        if (str_ends_with($value, $domain)) {
                            $fail('This email domain is not allowed.');
                        }
                    }
                    $restrictedUsernames = ['admin', 'root', 'superuser'];
                    $username = strtolower(explode('@', $value)[0]);
                    if (in_array($username, $restrictedUsernames)) {
                        $fail('This username is not allowed.');
                    }
                }
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                // Password complexity
                function($attribute, $value, $fail) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('Password must include at least one uppercase letter.');
                    }
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('Password must include at least one lowercase letter.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('Password must include at least one number.');
                    }
                    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
                        $fail('Password must include at least one special character.');
                    }
                }
            ]
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Attempt to authenticate and check user status
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check user status (case-insensitive comparison)
            switch (strtolower($user->status)) {
                case 'blocked':
                    Auth::logout();
                    return redirect()->back()->withErrors([
                        'email' => 'Your account has been blocked. Please contact the administrator.'
                    ]);
                case 'pending':
                    Auth::logout();
                    return redirect()->back()->withErrors([
                        'email' => 'Your account is pending approval. Please wait for administrator verification.'
                    ]);
                default:
                    // Update last login timestamp
                    $user->last_login_at = now();
                    $user->save();

                    // Regenerate session
                    $request->session()->regenerate();

                    // Log login attempt
                    Log::info('User logged in', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'last_login_at' => $user->last_login_at
                    ]);

                    // Redirect based on role - always to dashboard
                    return redirect('/');
            }
        }

        // Authentication failed
        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/'); // Redirect to front page with login modal
    }
}
