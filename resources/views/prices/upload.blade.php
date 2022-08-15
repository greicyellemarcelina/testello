@extends('layout')
@section('title','Upload Price Table')

@section('content')
    <form action="{{route('prices.upload')}}" method="post" enctype="multipart/form-data" class="col-4">
        <select name="client_id" class="form-select">
            @foreach($clients as $client)
                <option value="{{$client->id}}">{{$client->name}}</option>
            @endforeach
        </select>
        <div class="mb-3">
            <label for="formFile" class="form-label">Default file input example</label>
            <input class="form-control" type="file" name="file" id="formFile">
          </div>
        <button class="btn btn-primary">Upload</button>
    </form>
@endsection