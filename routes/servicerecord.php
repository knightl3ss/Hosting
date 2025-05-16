<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceRecordController\EmployeeController;
use App\Http\Controllers\ServiceRecordController\ServiceRecordController;
use App\Http\Controllers\ServiceRecordController\PrintController;
use App\Http\Controllers\ServiceRecordController\RecordPurposeController;
use App\Http\Controllers\ServiceRecordController\ServiceRecordFilterController;
use App\Http\Controllers\AuthController\RegisterController;

Route::get('/list_of_employee', function () {
    return view('Pages.Service_records.list_of_employee');
});

Route::get('/service_records', [EmployeeController::class, 'index'])->name('service_records');

Route::get('/Employee_records/{id?}', [EmployeeController::class, 'showEmployeeRecord'])->name('Employee_records');

// New route for storing service records
Route::post('/Employee_records/{id}/service', [ServiceRecordController::class, 'storeServiceRecord'])->name('store.service_record');

// New route for updating service records
Route::put('/Employee_records/{id}/service/update', [ServiceRecordController::class, 'updateServiceRecord'])->name('update.service_record');

Route::get('/account_list', [RegisterController::class, 'accountList'])->name('account_list');
Route::get('/view_account/{id}', [RegisterController::class, 'viewAccount'])->name('view_account');
Route::get('/edit_account/{id}', [RegisterController::class, 'editAccount'])->name('edit_account');
Route::put('/update_account/{id}', [RegisterController::class, 'updateAccount'])->name('update_account');
Route::post('/delete_account/{id}', [RegisterController::class, 'deleteAccount'])->name('delete_account');
Route::post('/block_account/{id}', [RegisterController::class, 'blockAccount'])->name('block_account');
Route::post('/unblock_account/{id}', [RegisterController::class, 'unblockAccount'])->name('unblock_account');

Route::get('/print-employee-records/{id}', [PrintController::class, 'printEmployeeRecords'])->name('print_employee_records');

// Service Record Purpose Routes
Route::get('/record_purpose/{id}', [RecordPurposeController::class, 'index'])->name('record_purpose');
Route::post('/record_purpose/store', [RecordPurposeController::class, 'store'])->name('record_purpose.store');
Route::post('/record_purpose/{id}/complete', [RecordPurposeController::class, 'updateStatus'])->name('record_purpose.complete');

// Add a route for the service record filter (for search and filter by employment status)
Route::get('/service_records/filter', [ServiceRecordFilterController::class, 'filter'])->name('service_records.filter');

// Add route for deleting service records
Route::delete('/service-record/{id}', [ServiceRecordController::class, 'destroy'])->name('delete_service_record');
