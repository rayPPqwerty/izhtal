<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\ScheduleViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'home'])->name('home');

Route::get('/documents', [App\Http\Controllers\HomeController::class, 'documents'])->name('documents');
Route::get('/documents/figure-skating', [App\Http\Controllers\HomeController::class, 'skating'])->name('skating');
Route::get('/contacts', [App\Http\Controllers\HomeController::class, 'contacts'])->name('contacts');
Route::get('/gallery', [App\Http\Controllers\HomeController::class, 'gallery'])->name('gallery');
Route::get('/schedule-plan', [ScheduleViewerController::class, 'show'])->name('schedule.public');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/bookings', [ScheduleController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/{booking}', [ScheduleController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [ScheduleController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/days/toggle-publish', [ScheduleController::class, 'togglePublish'])->name('days.toggle-publish');
});