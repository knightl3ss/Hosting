<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed regular user
        if (!User::where('email', 'admin2025@gmail.com')->exists()) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'middle_name' => '',
                'username' => 'admin',
                'age' => 30,
                'birthday' => '1994-01-01',
                'status' => 'Active',
                'address_street' => '456 User Lane',
                'address_city' => 'User City',
                'address_state' => 'User State',
                'address_postal_code' => '67890',
                'email' => 'admin2025@gmail.com',
                'role' => 'Admin',
                'password' => Hash::make('Password2025!'),
                'phone_number' => '0987654321',
                'gender' => 'male',
                'employee_id' => 'ADMIN001',
                'profile_picture' => 'default-profile.png',
            ]);
        }
    }
}
