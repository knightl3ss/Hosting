<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SecurityService
{
    /**
     * Encrypt data using AES-256-CBC
     *
     * @param mixed $data
     * @return string|null
     */
    public static function encrypt($data)
    {
        if (empty($data)) {
            return null;
        }
        
        try {
            return Crypt::encrypt($data);
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Decrypt data using AES-256-CBC
     *
     * @param string $encryptedData
     * @return mixed|null
     */
    public static function decrypt($encryptedData)
    {
        if (empty($encryptedData)) {
            return null;
        }
        
        try {
            return Crypt::decrypt($encryptedData);
        } catch (\Exception $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate a secure random token
     *
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32)
    {
        return Str::random($length);
    }
    
    /**
     * Hash sensitive data (one-way)
     *
     * @param string $data
     * @return string
     */
    public static function hash($data)
    {
        return hash('sha256', $data);
    }
    
    /**
     * Sanitize input data to prevent XSS attacks
     *
     * @param string $input
     * @return string
     */
    public static function sanitize($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Mask sensitive data for display (e.g., credit card numbers, SSNs)
     *
     * @param string $data
     * @param int $visibleChars Number of characters to leave visible at the end
     * @return string
     */
    public static function maskData($data, $visibleChars = 4)
    {
        if (empty($data) || strlen($data) <= $visibleChars) {
            return $data;
        }
        
        $maskedLength = strlen($data) - $visibleChars;
        $maskedPart = str_repeat('*', $maskedLength);
        $visiblePart = substr($data, -$visibleChars);
        
        return $maskedPart . $visiblePart;
    }
}
