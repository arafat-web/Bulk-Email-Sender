<?php

namespace App\Http\Controllers;

use App\Models\EmailUnsubscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class UnsubscribeController extends Controller
{
    /**
     * Show the unsubscribe confirmation page (GET from email footer link).
     * Signed URL protects against forged unsubscribes.
     */
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        $email = strtolower(trim($request->query('email', '')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(400, 'Invalid email address.');
        }

        $already = EmailUnsubscribe::isUnsubscribed($email);

        return view('unsubscribe.show', compact('email', 'already'));
    }

    /**
     * Confirm unsubscribe from the confirmation page (POST).
     * Enforces the signed URL so only genuine footer links can unsubscribe.
     */
    public function store(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        EmailUnsubscribe::unsubscribe($request->email, 'footer_link');

        return view('unsubscribe.done', ['email' => strtolower(trim($request->email))]);
    }

    /**
     * RFC 8058 one-click unsubscribe (POST from Gmail / Yahoo / Outlook).
     * CSRF-exempt (see VerifyCsrfToken::$except); the signed URL is the
     * forgery protection, so enforce it here too.
     */
    public function oneClick(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response('Invalid signature', 403);
        }

        $email = strtolower(trim($request->input('email', $request->query('email', ''))));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response('Invalid email', 400);
        }

        EmailUnsubscribe::unsubscribe($email, 'one_click');

        return response('Unsubscribed', 200);
    }
}
