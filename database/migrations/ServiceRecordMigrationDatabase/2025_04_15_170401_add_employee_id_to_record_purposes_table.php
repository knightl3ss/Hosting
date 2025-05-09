<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('record_purposes', function (Blueprint $table) {
            if (!Schema::hasColumn('record_purposes', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->after('id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('record_purposes', function (Blueprint $table) {
            if (Schema::hasColumn('record_purposes', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
        });
    }
};
