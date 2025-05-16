<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('rate_per_day')->nullable();
            $table->date('employment_start')->nullable();
            $table->date('employment_end')->nullable();
            $table->string('source_of_fund')->nullable();
            $table->string('office_assignment')->nullable();
            $table->string('appointment_type')->nullable(); // To differentiate between Permanent, Temporary, Job Order
            $table->string('employee_id')->nullable();
            $table->string('item_no')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('extension_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('location')->nullable();
            $table->date('birthday')->nullable();
            $table->integer('age')->nullable();
            $table->string('last_updated_by')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
