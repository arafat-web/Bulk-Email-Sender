<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/one/time/sender', [App\Http\Controllers\HomeController::class, 'oneTiemSender'])->name('one.time.sender');
Route::get('/saved/sender', [App\Http\Controllers\HomeController::class, 'savedSender'])->name('saved.sender');
