<?php

namespace App\Http\Controllers;

use App\Imports\ImportData;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OneTimeSenderController extends Controller
{
    public function import(Request $request)
    {
        Excel::import(new ImportData, request()->file('file'));
        return back();
    }
}
