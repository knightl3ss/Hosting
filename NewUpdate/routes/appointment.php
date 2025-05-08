<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController\AppointmentController;
use App\Http\Controllers\AppointmentController\AppointmentScheduleController;
use App\Http\Controllers\AppointmentController\AppointmentFileController;
use App\Http\Controllers\AppointmentController\AppointmentDocumentController;
use App\Http\Controllers\AppointmentController\AppointmentShowController;

// Appointment routes
Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
Route::get('/appointment/schedule', [AppointmentScheduleController::class, 'showSchedule'])->name('appointment.schedule');
Route::post('/appointments/upload', [AppointmentFileController::class, 'uploadFile'])->name('appointment.upload');
Route::get('/appointments/download/{id}', [AppointmentFileController::class, 'downloadFile'])->name('appointment.download');
Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointment.store');
Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
Route::delete('/appointment/file/{id}', [AppointmentFileController::class, 'deleteFile'])->name('appointment.deleteFile');
Route::get('/appointments/{id}', [AppointmentShowController::class, 'show'])->name('appointments.show');
Route::get('/appointments/download-sample-csv', [AppointmentController::class, 'downloadSampleCsv'])->name('appointments.downloadSampleCsv');
Route::post('/appointments/import', [AppointmentController::class, 'import'])->name('appointment.import');
Route::delete('/appointments/destroy-employee/{employee_id}', [AppointmentController::class, 'destroyEmployee'])->name('appointments.destroyEmployee');

// Update personal details for an appointment
Route::put('/appointments/{id}/update-personal-details', [AppointmentController::class, 'updatePersonalDetails'])->name('appointment.updatePersonalDetails');

// Appointment Document route (now by id)
Route::get('/appointments/document/{id}', [AppointmentDocumentController::class, 'show'])->name('appointments.document');

// Direct download route that bypasses middleware
Route::get('/direct-download/{id}', function($id) {
    try {
        $file = App\Models\AppointmentModel\File::findOrFail($id);
        $path = storage_path('app/public/' . $file->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        // Get file extension and set appropriate content type
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $contentTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];
        $contentType = $contentTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        // Prepare download filename
        $fileName = $file->filename;
        if (!str_contains($fileName, '.')) {
            $fileName .= '.' . $extension;
        }
        
        // Use raw PHP for download
        ob_end_clean(); // Clear output buffer
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    } catch (\Exception $e) {
        abort(500, 'File download error');
    }
})->name('direct.download')->middleware('web');

// Raw file download route without middleware
Route::get('/raw-download/{id}', function($id) {
    try {
        $file = App\Models\AppointmentModel\File::findOrFail($id);
        $path = storage_path('app/public/' . $file->file_path);
        
        if (!file_exists($path)) {
            echo 'File not found';
            exit;
        }
        
        // Get file info
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $fileName = $file->filename;
        if (!str_contains($fileName, '.')) {
            $fileName .= '.' . $extension;
        }
        
        // Set headers and output file using PHP directly
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    } catch (\Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit;
    }
})->name('raw.download');

// Temporary route to redirect employee.index to appointments (for compatibility)
Route::get('/employee/list', function() {
    return redirect()->route('appointments');
})->name('employee.index');
