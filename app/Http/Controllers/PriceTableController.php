<?php

namespace App\Http\Controllers;

use App\Jobs\PriceTableProcessingJob;
use App\Models\Client;
use App\Models\PriceUpload;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PriceTableController extends Controller
{
    public function upload(Request $request)
    {
        $uuid = Uuid::uuid4();

        $fileName = $uuid . '-' . $request->file('file')->getClientOriginalName(); // get file name
        $storedFile = $request->file('file')->storeAs('processing', $fileName); // store uploaded file to storage

        $upload = new PriceUpload();
        $upload->id = $uuid;
        $upload->status = 'waiting';
        $upload->client_id = $request->client_id;

        $upload->save();

        dispatch(new PriceTableProcessingJob($uuid, $storedFile));
        return redirect()->route('prices.list');
    }

    public function list() {
        $uploads = PriceUpload::orderBy('created_at', 'desc')->take(10)->get();
        return view('prices.list',['uploads'=>$uploads]);
    }

    public function create() {
        $clients = Client::all();
        return view('prices.upload',['clients'=>$clients]);
    }
}
