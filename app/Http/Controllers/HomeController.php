<?php

namespace App\Http\Controllers;

use App\Models\OneTimeSender;
use App\Models\User;
use App\Models\EmailAccount;
use App\Models\EmailContact;
use App\Models\ContactTag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $total_time = OneTimeSender::all()->count();
        $total_sent = OneTimeSender::all()->sum('total_email_address');
        $total_user = User::all()->count();
        $total_email_accounts = EmailAccount::count();
        $operations = OneTimeSender::latest()->get()->take(10);

        // Contact statistics
        $total_contacts = EmailContact::where('user_id', auth()->id())->count();
        $active_contacts = EmailContact::where('user_id', auth()->id())->where('status', 'active')->count();
        $total_tags = ContactTag::where('user_id', auth()->id())->count();
        $recent_contacts = EmailContact::where('user_id', auth()->id())
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();

        // Email activity statistics
        $emails_sent_today = EmailContact::where('user_id', auth()->id())
            ->whereDate('last_emailed_at', today())
            ->count();

        return view('home', compact(
            'total_time',
            'total_sent',
            'total_user',
            'total_email_accounts',
            'operations',
            'total_contacts',
            'active_contacts',
            'total_tags',
            'recent_contacts',
            'emails_sent_today'
        ));
    }

    public function savedTemplates(){
        return redirect()->route('email-templates.index');
    }
}
