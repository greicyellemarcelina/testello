@extends('layout')
@section('title','Upload Monitor')

@section('content')
    <a class="btn btn-primary" href="{{route('prices.create')}}" role="button">Upload new table</a>

    <table class="table">
        <thead>
        <tr>
            <th>Client</th>
            <th>Upload date</th>
            <th>Finish date</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
      @foreach ($uploads as $upload)
        <tr>
            <td>{{$upload->client->name}}</td>
            <td>{{$upload->created_at->format('d/m/Y H:i:s')}}</td>
            @if ($upload->status == 'done' || $upload->status == 'fail') 
                <td>{{$upload->updated_at->format('d/m/Y H:i:s')}}</td>
            @else 
                <td>--</td>
            @endif
            <td>{{$upload->status}}</td>
        </tr>
      @endforeach
    </tbody>
    </table>
@endsection