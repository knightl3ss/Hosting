<?php

namespace App\Traits;

use App\Services\SecurityService;
use Illuminate\Support\Facades\Crypt;

trait EncryptsAttributes
{
    /**
     * Get the encrypted attributes for the model.
     *
     * @return array
     */
    public function getEncryptedAttributes()
    {
        return $this->encrypted ?? [];
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->getEncryptedAttributes()) && !is_null($value)) {
            return $this->decryptAttribute($value);
        }
        
        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getEncryptedAttributes()) && !is_null($value)) {
            $value = $this->encryptAttribute($value);
        }
        
        return parent::setAttribute($key, $value);
    }

    /**
     * Encrypt an attribute value using AES-256-CBC via SecurityService.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function encryptAttribute($value)
    {
        return SecurityService::encrypt($value);
    }

    /**
     * Decrypt an attribute value using AES-256-CBC via SecurityService.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function decryptAttribute($value)
    {
        try {
            return SecurityService::decrypt($value);
        } catch (\Exception $e) {
            return $value; // Return original value if decryption fails
        }
    }
}
