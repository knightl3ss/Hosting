<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixSalaryColumnPostgresql extends Migration
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
            // Force PostgreSQL to treat salary as TEXT without any type conversion
            DB::statement('ALTER TABLE service_records ALTER COLUMN salary DROP NOT NULL');
            DB::statement('ALTER TABLE service_records ALTER COLUMN salary TYPE TEXT USING salary::TEXT');
            
            // Add a comment to prevent future migrations from changing the type
            DB::statement("COMMENT ON COLUMN service_records.salary IS 'DO NOT CHANGE TYPE - Used for encrypted data'");
            
            // Log the change
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('manual_fix_salary_postgresql', (SELECT MAX(batch) FROM migrations) + 1)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No action needed for rollback as we want to keep it as text
    }
} 