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
        // Seed admin user
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'middle_name' => '',
                'username' => 'admin',
                'age' => 30,
                'birthday' => '1994-01-01',
                'status' => 'Active',
                'address_street' => '123 Admin Street',
                'address_city' => 'Admin City',
                'address_state' => 'Admin State',
                'address_postal_code' => '12345',
                'email' => 'admin@admin.com',
                'role' => 'Admin',
                'password' => Hash::make('password123'),
                'phone_number' => '1234567890',
                'gender' => 'male',
                'employee_id' => 'ADMIN001',
                'profile_picture' => 'default-profile.png',
            ]);
        }

        // Seed regular user
        if (!User::where('email', 'narismastem2019@gmail.com')->exists()) {
            User::create([
                'first_name' => 'Prince',
                'last_name' => 'User',
                'middle_name' => '',
                'username' => 'prince',
                'age' => 25,
                'birthday' => '2000-01-01',
                'status' => 'Active',
                'address_street' => '456 User Lane',
                'address_city' => 'User City',
                'address_state' => 'User State',
                'address_postal_code' => '67890',
                'email' => 'narismastem2019@gmail.com',
                'role' => 'Admin',
                'password' => Hash::make('Password123!'),
                'phone_number' => '0987654321',
                'gender' => 'male',
                'employee_id' => 'USER001',
                'profile_picture' => 'default-profile.png',
            ]);
        }
    }
}
