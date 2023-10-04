<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OneTimeSenderController;
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

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/one/time/sender', [HomeController::class, 'oneTiemSender'])->name('one.time.sender');
Route::get('/saved/sender', [HomeController::class, 'savedSender'])->name('saved.sender');


Route::post('/one/time', [OneTimeSenderController::class, 'import'])->name('ots.import');