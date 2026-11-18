<?php

use Illuminate\Support\Facades\Route;

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


