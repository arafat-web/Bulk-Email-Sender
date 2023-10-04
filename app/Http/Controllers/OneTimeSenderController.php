<?php

namespace App\Http\Controllers;

use App\Imports\ImportData;
use App\Mail\SendMail;
use App\Models\OneTimeSender;
use App\Models\TempMailAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Sleep;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class OneTimeSenderController extends Controller
{

    public function import(Request $request)
    {

        $request->validate([
            'file' => 'required|file',
            'subject' => 'required',
            'body' => 'required',
        ], [
            'file.required' => 'Please upload a file.',
            'file.file' => 'The uploaded file is not valid.',
            'subject.required' => 'Please enter a subject.',
            'body.required' => 'Please enter the body of the email.',
        ]);

        if (Excel::import(new ImportData, request()->file('file'))) {

            OneTimeSender::create([
                'filename' => $request->file->getClientOriginalName(),
                'total_email_address' => TempMailAddress::all()->count(),
            ]);
            $total_send = TempMailAddress::all()->count();

            $mailData = [
                'subject' => $request->subject,
                'body' => $request->body,
            ];
            $getEmailAddress = TempMailAddress::all('email');
            foreach ($getEmailAddress as $value) {
                Mail::to($value->email)->send(new SendMail($mailData));
                Sleep::for(10)->milliseconds();
            }
            TempMailAddress::truncate();
            return back()->with([
                'message' => 'Success! Email Sent Succesfully. Total Sent: ' . $total_send,
            ]);
        } else {
            return back()->with('error', 'Failed! Something went wrong.');
        }

    }
}