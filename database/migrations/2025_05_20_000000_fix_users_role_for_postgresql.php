<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixUsersRoleForPostgresql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if we're running PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // First check if the role column exists
            if (Schema::hasColumn('users', 'role')) {
                // For PostgreSQL, explicitly set accepted values for the role enum
                // Drop constraints if they exist
                DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
                
                // Add constraint that accepts both 'admin' and 'Admin'
                DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'Admin'))");
                
                // Update any lowercase roles to match casing in the local environment
                DB::statement("UPDATE users SET role = 'Admin' WHERE role = 'admin'");
                
                // Log the change
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('manual_fix_users_role_postgresql', (SELECT MAX(batch) FROM migrations) + 1)");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No specific action needed for rollback
    }
} 