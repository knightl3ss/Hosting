<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateRatePerDayColumnInAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, we'll handle databases like PostgreSQL that need a schema change
        if (DB::getDriverName() === 'pgsql') {
            // For PostgreSQL, we need to:
            // 1. Create a new column with the correct type
            // 2. Copy data (and potentially convert it)
            // 3. Drop the old column
            // 4. Rename the new column to the original name
            
            Schema::table('appointments', function (Blueprint $table) {
                $table->decimal('rate_per_day_new', 15, 2)->nullable()->after('rate_per_day');
            });
            
            // Copy and convert decryptable data
            $appointments = DB::table('appointments')->get();
            foreach ($appointments as $appointment) {
                try {
                    // Try to decrypt the value if it's encrypted
                    $value = $appointment->rate_per_day;
                    if (!is_null($value) && !empty($value)) {
                        try {
                            $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($value);
                            // Convert to numeric and update
                            $numericValue = is_numeric($decrypted) ? $decrypted : 0;
                            DB::table('appointments')
                                ->where('id', $appointment->id)
                                ->update(['rate_per_day_new' => $numericValue]);
                        } catch (\Exception $e) {
                            // If decryption fails, assume it's already a number or empty
                            $numericValue = is_numeric($value) ? $value : 0;
                            DB::table('appointments')
                                ->where('id', $appointment->id)
                                ->update(['rate_per_day_new' => $numericValue]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue with migration
                    \Illuminate\Support\Facades\Log::error('Failed to process rate_per_day for appointment ID: ' . $appointment->id . ' - ' . $e->getMessage());
                }
            }
            
            // Drop the old column and rename the new one
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('rate_per_day');
            });
            
            Schema::table('appointments', function (Blueprint $table) {
                $table->renameColumn('rate_per_day_new', 'rate_per_day');
            });
        } else {
            // For other databases like MySQL that can do this in-place
            Schema::table('appointments', function (Blueprint $table) {
                $table->decimal('rate_per_day', 15, 2)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('rate_per_day')->nullable()->change();
        });
    }
} 