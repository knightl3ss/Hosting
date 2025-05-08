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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('appointments')->onDelete('cascade');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('designation')->nullable();
            $table->string('status')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('station')->nullable();
            $table->string('separation_date')->nullable();
            $table->string('service_status')->nullable();
            $table->boolean('is_permanent')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
