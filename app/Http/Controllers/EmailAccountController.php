<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of email accounts.
     */
    public function index()
    {
        $emailAccounts = EmailAccount::orderBy('is_default', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('email-accounts.index', compact('emailAccounts'));
    }

    /**
     * Show the form for creating a new email account.
     */
    public function create()
    {
        return view('email-accounts.create');
    }

    /**
     * Store a newly created email account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:email_accounts,email',
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string|min:1',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'from_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $emailAccount = EmailAccount::create($request->all());

        // If this is the first email account, make it default
        if (EmailAccount::count() === 1) {
            $emailAccount->setAsDefault();
        }

        return redirect()->route('email-accounts.index')
                        ->with('success', 'Email account created successfully.');
    }

    /**
     * Show the form for editing the specified email account.
     */
    public function edit(EmailAccount $emailAccount)
    {
        return view('email-accounts.edit', compact('emailAccount'));
    }

    /**
     * Update the specified email account.
     */
    public function update(Request $request, EmailAccount $emailAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:email_accounts,email,' . $emailAccount->id,
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|min:1',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'from_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = $request->all();

        // Only update password if provided
        if (empty($data['smtp_password'])) {
            unset($data['smtp_password']);
        }

        $emailAccount->update($data);

        return redirect()->route('email-accounts.index')
                        ->with('success', 'Email account updated successfully.');
    }

    /**
     * Set the specified email account as default.
     */
    public function setDefault(EmailAccount $emailAccount)
    {
        $emailAccount->setAsDefault();

        return redirect()->route('email-accounts.index')
                        ->with('success', 'Default email account updated successfully.');
    }

    /**
     * Toggle the active status of the specified email account.
     */
    public function toggleActive(EmailAccount $emailAccount)
    {
        // Don't allow deactivating the default account
        if ($emailAccount->is_default && $emailAccount->is_active) {
            return redirect()->route('email-accounts.index')
                            ->with('error', 'Cannot deactivate the default email account.');
        }

        $emailAccount->update(['is_active' => !$emailAccount->is_active]);

        $status = $emailAccount->is_active ? 'activated' : 'deactivated';

        return redirect()->route('email-accounts.index')
                        ->with('success', "Email account {$status} successfully.");
    }

    /**
     * Test the email configuration.
     */
    public function test(EmailAccount $emailAccount)
    {
        try {
            // Configure mail settings dynamically
            Config::set('mail.mailers.smtp.host', $emailAccount->smtp_host);
            Config::set('mail.mailers.smtp.port', $emailAccount->smtp_port);
            Config::set('mail.mailers.smtp.username', $emailAccount->smtp_username);
            Config::set('mail.mailers.smtp.password', $emailAccount->smtp_password);
            Config::set('mail.mailers.smtp.encryption', $emailAccount->smtp_encryption === 'none' ? null : $emailAccount->smtp_encryption);
            Config::set('mail.from.address', $emailAccount->email);
            Config::set('mail.from.name', $emailAccount->from_name);

            // Send test email
            Mail::raw('This is a test email from your Bulk Email Sender application.', function ($message) use ($emailAccount) {
                $message->to($emailAccount->email)
                        ->subject('Test Email - Bulk Email Sender');
            });

            return redirect()->route('email-accounts.index')
                            ->with('success', 'Test email sent successfully!');

        } catch (\Exception $e) {
            return redirect()->route('email-accounts.index')
                            ->with('error', 'Test email failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified email account.
     */
    public function destroy(EmailAccount $emailAccount)
    {
        // Don't allow deleting the default account if it's the only one
        if ($emailAccount->is_default && EmailAccount::active()->count() === 1) {
            return redirect()->route('email-accounts.index')
                            ->with('error', 'Cannot delete the only active email account.');
        }

        // If deleting the default account, set another one as default
        if ($emailAccount->is_default) {
            $newDefault = EmailAccount::where('id', '!=', $emailAccount->id)->active()->first();
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }

        $emailAccount->delete();

        return redirect()->route('email-accounts.index')
                        ->with('success', 'Email account deleted successfully.');
    }
}
