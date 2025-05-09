<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'birthday',
        'status',
        'address_street',
        'address_city',
        'address_state',
        'address_postal_code',
        'extension_name',
        'email',
        'username',
        'role',
        'password',
        'phone_number',
        'gender',
        'profile_picture',
        'employee_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Validation rules for the model.
     *
     * @var array<string, string>
     */
    protected $rules = [
        'address_street' => 'required|string|max:255',
        'address_city' => 'required|string|max:255',
        'address_state' => 'required|string|max:255',
        'address_postal_code' => 'required|string|max:10',
    ];
}
