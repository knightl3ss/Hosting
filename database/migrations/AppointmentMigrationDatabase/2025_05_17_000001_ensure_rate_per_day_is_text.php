<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EnsureRatePerDayIsText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only run this for PostgreSQL databases
        if (DB::getDriverName() === 'pgsql') {
            // First, explicitly change the column type to TEXT in PostgreSQL
            DB::statement('ALTER TABLE appointments ALTER COLUMN rate_per_day TYPE TEXT');
            
            // Then explicitly set it to accept NULL values
            DB::statement('ALTER TABLE appointments ALTER COLUMN rate_per_day DROP NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No action needed for rollback since changing back to string is handled by the main migration
    }
} 