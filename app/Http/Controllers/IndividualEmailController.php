<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use App\Jobs\SendIndividualEmailJob;
use Illuminate\Support\Facades\Validator;

class IndividualEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $emailAccounts = EmailAccount::where('is_active', true)->get();
        $templates = EmailTemplate::where('is_active', true)->get();

        if ($emailAccounts->isEmpty()) {
            return redirect()->route('email-accounts.index')->with('error', 'Please configure at least one email account before sending emails.');
        }

        return view('individual-emails.create', compact('emailAccounts', 'templates'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_account_id' => 'required|exists:email_accounts,id',
            'recipients' => 'required|string',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'send_type' => 'required|in:individual,bulk'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Parse and validate email addresses
        $emailList = $this->parseEmailAddresses($request->recipients);

        if (empty($emailList['valid'])) {
            return response()->json([
                'success' => false,
                'message' => 'No valid email addresses found.',
                'errors' => ['recipients' => ['Please provide at least one valid email address.']]
            ], 422);
        }

        $emailAccount = EmailAccount::findOrFail($request->email_account_id);
        $subject = $request->subject;
        $body = $request->body;
        $validEmails = $emailList['valid'];
        $invalidEmails = $emailList['invalid'];

        try {
            // Send emails based on type
            if ($request->send_type === 'individual') {
                // Send individual emails (each recipient gets their own email)
                foreach ($validEmails as $email) {
                    SendIndividualEmailJob::dispatch($emailAccount, $email, $subject, $body);
                }
            } else {
                // Send bulk email (all recipients in one email)
                SendIndividualEmailJob::dispatch($emailAccount, $validEmails, $subject, $body, true);
            }

            $response = [
                'success' => true,
                'message' => 'Emails queued successfully!',
                'summary' => [
                    'total_emails' => count($validEmails),
                    'valid_emails' => count($validEmails),
                    'invalid_emails' => count($invalidEmails),
                    'send_type' => $request->send_type
                ]
            ];

            if (!empty($invalidEmails)) {
                $response['invalid_emails'] = $invalidEmails;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue emails: ' . $e->getMessage()
            ], 500);
        }
    }

    private function parseEmailAddresses($emailString)
    {
        $valid = [];
        $invalid = [];

        // Split by common delimiters
        $emails = preg_split('/[,;\n\r\t]+/', $emailString);

        foreach ($emails as $email) {
            $email = trim($email);
            if (empty($email)) continue;

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            } else {
                $invalid[] = $email;
            }
        }

        return [
            'valid' => array_unique($valid),
            'invalid' => array_unique($invalid)
        ];
    }

    public function validateEmails(Request $request)
    {
        $emailList = $this->parseEmailAddresses($request->recipients ?? '');

        return response()->json([
            'valid_count' => count($emailList['valid']),
            'invalid_count' => count($emailList['invalid']),
            'valid_emails' => $emailList['valid'],
            'invalid_emails' => $emailList['invalid']
        ]);
    }
}
