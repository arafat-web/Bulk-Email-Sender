<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstantCampaignController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndividualEmailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactTagController;
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

Auth::routes(['register' => false, 'verify' => false, 'reset' => false]);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/instant/campaign', [InstantCampaignController::class, 'create'])->name('instant.campaign.create');
Route::post('/instant/campaign', [InstantCampaignController::class, 'import'])->name('instant.campaign.import');
Route::get('/saved/templates', [HomeController::class, 'savedTemplates'])->name('saved.templates');

// Email Account Management Routes
Route::resource('email-accounts', EmailAccountController::class)->except(['show']);
Route::post('/email-accounts/{emailAccount}/set-default', [EmailAccountController::class, 'setDefault'])->name('email-accounts.set-default');
Route::post('/email-accounts/{emailAccount}/toggle-active', [EmailAccountController::class, 'toggleActive'])->name('email-accounts.toggle-active');
Route::post('/email-accounts/{emailAccount}/test', [EmailAccountController::class, 'test'])->name('email-accounts.test');

// Email Template Management Routes
Route::resource('email-templates', EmailTemplateController::class);
Route::post('/email-templates/{emailTemplate}/toggle-active', [EmailTemplateController::class, 'toggleActive'])->name('email-templates.toggle-active');
Route::post('/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
Route::get('/api/email-templates/{emailTemplate}', [EmailTemplateController::class, 'getTemplate'])->name('email-templates.get');

// Profile Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings/password', [ProfileController::class, 'showPasswordForm'])->name('settings.password');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password.update');
});

// Individual Email Routes
Route::middleware('auth')->group(function () {
    Route::get('/individual-emails', [IndividualEmailController::class, 'create'])->name('individual-emails.create');
    Route::post('/individual-emails/send', [IndividualEmailController::class, 'send'])->name('individual-emails.send');
    Route::post('/individual-emails/validate', [IndividualEmailController::class, 'validateEmails'])->name('individual-emails.validate');
});

// Contact Management Routes
Route::middleware('auth')->group(function () {
    Route::get('/contacts/import/form', [ContactController::class, 'importForm'])->name('contacts.import.form');
    Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::post('/contacts/bulk-action', [ContactController::class, 'bulkAction'])->name('contacts.bulk-action');
    Route::resource('contacts', ContactController::class);
});

// Contact Tag Management Routes
Route::middleware('auth')->group(function () {
    Route::resource('tags', ContactTagController::class)->except(['show']);
});
