<?php

namespace App\Http\Controllers;

use App\Imports\ImportData;
use App\Jobs\SendEmailJob;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Models\OneTimeSender;
use App\Models\TempMailAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InstantCampaignController extends Controller
{
    /**
     * Show the instant campaign creation form
     */
    public function create(Request $request)
    {
        $templates = EmailTemplate::active()->orderBy('name')->get();
        $selectedTemplate = null;

        // Check if a template was selected via query parameter
        if ($request->has('template')) {
            $selectedTemplate = EmailTemplate::active()->find($request->get('template'));
        }

        return view('instant-campaign.create', compact('templates', 'selectedTemplate'));
    }

    public function import(Request $request)
    {
        try {
            // Check if there's a default email account configured
            $defaultEmailAccount = EmailAccount::getDefault();

            if (! $defaultEmailAccount) {
                return back()->with('error', 'No default email account configured. Please add and configure an email account first.');
            }

            // Validate the request with custom messages
            $request->validate([
                'file' => 'required|file|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv|max:10240',
                'subject' => 'required|string|max:255',
                'body' => 'required|string|max:65535',
                'template_id' => 'nullable|exists:email_templates,id',
            ], [
                'file.required' => 'Please upload an Excel or CSV file.',
                'file.file' => 'The uploaded file is not valid.',
                'file.mimetypes' => 'Please upload a valid Excel (.xlsx, .xls) or CSV (.csv) file.',
                'file.max' => 'The file size must not exceed 10MB.',
                'subject.required' => 'Please enter an email subject.',
                'subject.max' => 'Subject must not exceed 255 characters.',
                'body.required' => 'Please enter the email content.',
                'body.max' => 'Email content is too long. Please reduce the content size.',
                'template_id.exists' => 'Selected template not found.',
            ]);

            // Clear any existing temporary data for this user only (scoped delete, not global truncate)
            TempMailAddress::forUser(auth()->id())->delete();

            // Track template usage if a template was used
            $usedTemplate = null;
            if ($request->filled('template_id')) {
                $usedTemplate = EmailTemplate::find($request->template_id);
                if ($usedTemplate) {
                    $usedTemplate->incrementUsage();
                }
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Import the Excel file
                Excel::import(new ImportData(auth()->id()), $request->file('file'));

                $emailCount = TempMailAddress::forUser(auth()->id())->count();

                if ($emailCount === 0) {
                    DB::rollBack();

                    return back()->with('error', 'No valid email addresses found in the uploaded file. Please ensure email addresses are in the 3rd column (Column C).');
                }

                // Check for reasonable limits (prevent abuse)
                if ($emailCount > 10000) {
                    DB::rollBack();

                    return back()->with('error', 'Too many email addresses ('.number_format($emailCount).'). Maximum allowed is 10,000 per campaign.');
                }

                // Create campaign record
                $campaign = OneTimeSender::create([
                    'file_name' => $request->file->getClientOriginalName(),
                    'total_email_address' => $emailCount,
                    'subject' => $request->subject,
                    'status' => 'processing',
                    'created_at' => now(),
                ]);

                $mailData = [
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'email_account_id' => $defaultEmailAccount->id,
                    'campaign_id' => $campaign->id,
                ];

                // Dispatch email jobs using query chunk (avoids loading all 10k into memory)
                $batchSize = 100;
                $totalBatches = (int) ceil($emailCount / $batchSize);

                Log::info('Starting instant campaign', [
                    'campaign_id' => $campaign->id,
                    'total_emails' => $emailCount,
                    'batches' => $totalBatches,
                    'email_account' => $defaultEmailAccount->name,
                ]);

                $queueConnection = config('queue.default');
                $useDelay = $queueConnection !== 'sync';

                TempMailAddress::forUser(auth()->id())->select('id', 'email')->chunkById(500, function ($rows) use ($mailData, $useDelay) {
                    foreach ($rows as $row) {
                        if (! filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
                            Log::warning('Invalid email address skipped: '.$row->email);
                            continue;
                        }
                        $job = SendEmailJob::dispatch($row->email, $mailData);
                        if ($useDelay) {
                            $job->onQueue('emails')->delay(rand(1, 5));
                        }
                    }
                });

                // Update campaign status
                $campaign->update(['status' => 'queued']);

                // Touch last_used_at now; emails_sent is incremented per-job in SendEmailJob
                // to avoid double-counting (previously incremented here AND per job)
                $defaultEmailAccount->update(['last_used_at' => now()]);

                DB::commit();

                // Clear temporary data for this user only
                TempMailAddress::forUser(auth()->id())->delete();

                return back()->with([
                    'message' => 'Success! Instant campaign launched successfully using "'.$defaultEmailAccount->name.'". '.number_format($emailCount).' emails have been queued for delivery.',
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Failed to process instant campaign', [
                    'error' => $e->getMessage(),
                    'file' => $request->file->getClientOriginalName(),
                    'user_id' => auth()->id(),
                ]);

                return back()->with('error', 'Failed to process the campaign. Please check your file format and try again.');
            }

        } catch (Exception $e) {
            Log::error('Instant campaign validation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
