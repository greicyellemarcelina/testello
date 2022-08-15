<?php

namespace App\Http\Controllers;

use App\Jobs\PriceTableProcessingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PriceTableController extends Controller
{
    public function upload(Request $request)
    {
        $fileName = $request->file('file')->getClientOriginalName(); // get file name
        $storedFile = $request->file('file')->storeAs('processing', $fileName); // store uploaded file to storage

        dispatch(new PriceTableProcessingJob($storedFile));
        return 'ok';
    }
}
