<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, try to drop the existing check constraint
        try {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            Log::info('Dropped existing users_role_check constraint');
        } catch (\Exception $e) {
            Log::error('Error dropping users_role_check constraint: ' . $e->getMessage());
        }

        // Now standardize the admin roles with quotes
        try {
            // Check for Admin role and update to admin
            $count = DB::table('users')
                ->where('role', 'Admin')
                ->count();
            
            if ($count > 0) {
                Log::info("Found {$count} users with role 'Admin'");
                
                // Use raw SQL with proper quoting
                DB::statement("UPDATE users SET role = 'admin' WHERE role = 'Admin'");
                Log::info("Updated {$count} admin users to lowercase 'admin'");
            }
        } catch (\Exception $e) {
            Log::error('Error updating admin roles: ' . $e->getMessage());
        }
        
        // If needed, create a new check constraint with proper values
        try {
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'user', 'employee', 'manager'))");
            Log::info('Added new users_role_check constraint with proper values');
        } catch (\Exception $e) {
            Log::error('Error adding new users_role_check constraint: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed
    }
}; 