<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IncreaseUsersEncryptedFieldsLength extends Migration
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
            // Direct PostgreSQL commands to change column types to TEXT
            // This avoids requiring doctrine/dbal package
            DB::statement('ALTER TABLE users ALTER COLUMN middle_name TYPE TEXT');
            DB::statement('ALTER TABLE users ALTER COLUMN phone_number TYPE TEXT');
            DB::statement('ALTER TABLE users ALTER COLUMN address_street TYPE TEXT');
            DB::statement('ALTER TABLE users ALTER COLUMN address_city TYPE TEXT');
            DB::statement('ALTER TABLE users ALTER COLUMN address_state TYPE TEXT');
            DB::statement('ALTER TABLE users ALTER COLUMN address_postal_code TYPE TEXT');
            
            // Log the change
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('manual_increase_users_fields_length', (SELECT MAX(batch) FROM migrations) + 1)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't revert this change to prevent data loss
    }
} 