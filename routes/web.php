<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\ScheduleViewerController;
use App\Http\Controllers\Admin\RentalRequestController;
use App\Http\Controllers\PublicRentalRequestController;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('/documents', [App\Http\Controllers\HomeController::class, 'documents'])->name('documents');
Route::get('/documents/figure-skating', [App\Http\Controllers\HomeController::class, 'skating'])->name('skating');
Route::get('/contacts', [App\Http\Controllers\HomeController::class, 'contacts'])->name('contacts');
Route::get('/gallery', [App\Http\Controllers\HomeController::class, 'gallery'])->name('gallery');

Route::get('/schedule-plan', [ScheduleViewerController::class, 'show'])->name('schedule.public');
Route::post('/rental-request', [PublicRentalRequestController::class, 'store'])->name('rental-request.store');
Route::get('/privacy', [App\Http\Controllers\HomeController::class, 'privacy'])->name('privacy');
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/bookings', [ScheduleController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/{booking}', [ScheduleController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [ScheduleController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/days/toggle-publish', [ScheduleController::class, 'togglePublish'])->name('days.toggle-publish');
    Route::get('/requests', [RentalRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{id}/status', [RentalRequestController::class, 'updateStatus'])->name('requests.update-status');
    Route::post('/requests/{id}/approve', [RentalRequestController::class, 'approve'])->name('requests.approve');
    Route::delete('/requests/{id}', [RentalRequestController::class, 'destroy'])->name('requests.destroy');
});