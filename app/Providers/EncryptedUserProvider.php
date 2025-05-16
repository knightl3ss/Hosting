<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Services\AuthService;

class EncryptedUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || 
           !isset($credentials['email']) || 
           (count($credentials) === 1 && array_key_exists('password', $credentials))) {
            return null;
        }

        // Use our custom service to find user with encrypted email
        return AuthService::findUserByEmail($credentials['email']);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        
        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
