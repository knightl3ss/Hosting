<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class AuthService
{
    /**
     * Find a user by their email, handling encryption.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public static function findUserByEmail($email)
    {
        // Get all users and find the one with matching decrypted email
        return User::all()->first(function ($user) use ($email) {
            try {
                return $user->email === $email;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Validate user credentials with encrypted email.
     *
     * @param array $credentials
     * @return \App\Models\User|null
     */
    public static function validateCredentials(array $credentials)
    {
        $user = self::findUserByEmail($credentials['email']);
        
        if (!$user) {
            return null;
        }
        
        if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return null;
        }
        
        return $user;
    }
}
