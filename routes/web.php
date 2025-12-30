<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotamController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\PassAlongController;
use App\Http\Controllers\PassAlongAttachmentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth + Verified Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Work Orders
    |--------------------------------------------------------------------------
    */
    Route::resource('work-orders', WorkOrderController::class);

    /*
    |--------------------------------------------------------------------------
    | Inspections (FAA Part 139)
    |--------------------------------------------------------------------------
    */
    Route::resource('inspections', InspectionController::class);

    Route::post('inspections/{inspection}/certify', [InspectionController::class, 'certify'])
        ->name('inspections.certify');

    Route::post('inspections/{inspection}/unlock', [InspectionController::class, 'unlock'])
        ->name('inspections.unlock');

    Route::get('inspections/{inspection}/print', [InspectionController::class, 'print'])
        ->name('inspections.print');

    Route::get('inspections/{inspection}/pdf', [InspectionController::class, 'pdf'])
        ->name('inspections.pdf');

    Route::get('inspections-print', [InspectionController::class, 'printIndex'])
        ->name('inspections.printIndex');

    Route::get('inspections-pdf', [InspectionController::class, 'pdfIndex'])
        ->name('inspections.pdfIndex');

    /*
    |--------------------------------------------------------------------------
    | PASS ALONG
    |--------------------------------------------------------------------------
    */
    Route::resource('pass-alongs', PassAlongController::class);

    Route::post('pass-alongs/{pass_along}/submit', [PassAlongController::class, 'submit'])
        ->name('pass-alongs.submit');

    Route::post('pass-alongs/{pass_along}/unlock', [PassAlongController::class, 'unlock'])
        ->name('pass-alongs.unlock');

    Route::get('pass-alongs/{pass_along}/print', [PassAlongController::class, 'print'])
        ->name('pass-alongs.print');

    Route::get('pass-alongs/{pass_along}/pdf', [PassAlongController::class, 'pdf'])
        ->name('pass-alongs.pdf');

    // ✅ Attachments (scoped to Pass Along)
    Route::post('pass-alongs/{pass_along}/attachments', [PassAlongController::class, 'uploadAttachment'])
        ->name('pass-alongs.attachments.upload');

    // IMPORTANT: download is handled by PassAlongAttachmentController
    Route::get('pass-alongs/{pass_along}/attachments/{attachment}/download', [PassAlongAttachmentController::class, 'download'])
        ->name('pass-alongs.attachments.download');

    Route::delete('pass-alongs/{pass_along}/attachments/{attachment}', [PassAlongController::class, 'deleteAttachment'])
        ->name('pass-alongs.attachments.delete');

    /*
    |--------------------------------------------------------------------------
    | NOTAM Tracker
    |--------------------------------------------------------------------------
    */
    Route::resource('notams', NotamController::class);

    Route::post('notams/{notam}/activate', [NotamController::class, 'activate'])
        ->name('notams.activate');

    Route::post('notams/{notam}/cancel', [NotamController::class, 'cancel'])
        ->name('notams.cancel');

    Route::post('notams/{notam}/lock', [NotamController::class, 'lock'])
        ->name('notams.lock');

    Route::post('notams/{notam}/unlock', [NotamController::class, 'unlock'])
        ->name('notams.unlock');

    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    */
    Route::get('audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index');

    Route::get('audit-logs/export/csv', [AuditLogController::class, 'exportCsv'])
        ->name('audit-logs.export.csv');

    Route::get('audit-logs/export/pdf', [AuditLogController::class, 'exportPdf'])
        ->name('audit-logs.export.pdf');

    Route::get('audit-logs/print', [AuditLogController::class, 'print'])
        ->name('audit-logs.print');

    /*
    |--------------------------------------------------------------------------
    | Admin – User Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show']);
    });
});

/*
|--------------------------------------------------------------------------
| Profile (auth only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/
Route::get('/health', [HealthController::class, 'show'])->name('health');


Route::get('/make-admin', function () {
    $user = \App\Models\User::where('email', 'youremail@example.com')->first();
    $user->assignRole('admin');
    return 'done';
});