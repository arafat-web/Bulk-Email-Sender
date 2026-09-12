<?php

use App\Http\Controllers\CampaignTrackerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactTagController;
use App\Http\Controllers\EmailAccountController;
use App\Http\Controllers\EmailKeywordController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndividualEmailController;
use App\Http\Controllers\InstantCampaignController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Support\Facades\Auth;
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

// Route::get('/', function () {
//     return view('home');
// });

Auth::routes(['register' => false, 'verify' => false, 'reset' => false]);

// All application routes require authentication
Route::middleware('auth')->group(function () {
    // Dashboard & Home Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/instant/campaign', [InstantCampaignController::class, 'create'])->name('instant.campaign.create');
    Route::post('/instant/campaign', [InstantCampaignController::class, 'import'])->middleware('throttle:5,1')->name('instant.campaign.import');
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

    // Keyword Config — admin-defined dynamic placeholders like [company_name], [name], [phone], [email]
    Route::resource('keywords', EmailKeywordController::class)->except(['show']);
    Route::post('/keywords/{keyword}/toggle-active', [EmailKeywordController::class, 'toggleActive'])->name('keywords.toggle-active');
    Route::post('/keywords/preview', [EmailKeywordController::class, 'preview'])->name('keywords.preview');
    Route::get('/api/keywords', [EmailKeywordController::class, 'list'])->name('keywords.list');

    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings/password', [ProfileController::class, 'showPasswordForm'])->name('settings.password');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password.update');

    // Individual Email Routes
    Route::get('/individual-emails', [IndividualEmailController::class, 'create'])->name('individual-emails.create');
    Route::post('/individual-emails/send', [IndividualEmailController::class, 'send'])->middleware('throttle:10,1')->name('individual-emails.send');
    Route::post('/individual-emails/validate', [IndividualEmailController::class, 'validateEmails'])->middleware('throttle:30,1')->name('individual-emails.validate');

    // Contact Management Routes
    Route::get('/contacts/import/form', [ContactController::class, 'importForm'])->name('contacts.import.form');
    Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::post('/contacts/bulk-action', [ContactController::class, 'bulkAction'])->name('contacts.bulk-action');
    Route::get('/contacts/ids', [ContactController::class, 'ids'])->name('contacts.ids');
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::resource('contacts', ContactController::class);

    // Realtime campaign tracker — live counters, per-recipient status, retry.
    Route::get('/campaigns', [CampaignTrackerController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{campaign}', [CampaignTrackerController::class, 'show'])->name('campaigns.show');
    Route::get('/campaigns/{campaign}/live', [CampaignTrackerController::class, 'live'])->name('campaigns.live');
    Route::get('/campaigns/{campaign}/feed', [CampaignTrackerController::class, 'feed'])->name('campaigns.feed');
    Route::post('/campaigns/{campaign}/retry-failed', [CampaignTrackerController::class, 'retryFailed'])->name('campaigns.retry-failed');

    // Contact Tag Management Routes
    Route::resource('tags', ContactTagController::class)->except(['show']);
});

// Public unsubscribe routes (NO auth — recipients click from their inbox).
// GET shows a confirmation page, POST confirms, /one-click serves Gmail/Yahoo RFC 8058.
Route::get('/unsubscribe', [UnsubscribeController::class, 'show'])->name('unsubscribe.show');
Route::post('/unsubscribe', [UnsubscribeController::class, 'store'])->name('unsubscribe.store');
Route::post('/unsubscribe/one-click', [UnsubscribeController::class, 'oneClick'])->name('unsubscribe.one-click');
