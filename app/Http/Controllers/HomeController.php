<?php

namespace App\Http\Controllers;

use App\Models\OneTimeSender;
use App\Models\User;
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
        $operations = OneTimeSender::latest()->get()->take(10);
        return view('home', compact('total_time', 'total_sent', 'total_user', 'operations'));
    }

    public function oneTiemSender(){
        return view('one-time-sender');
    }

    public function savedSender(){
        return view('saved-sender');
    }
}
