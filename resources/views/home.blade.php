@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="card">
            {{-- <div class="card-header">Controls</div> --}}
            <div class="card-body">
                <h4 class="text-center w-100">Session: <span class="text-primary">00:00<span></h4>
                <div class="small text-center w-100 my-2">Next Post: <span class="text-primary">00:00<span></div>
                <a href="{{ url('/run') }}" class="btn btn-lg btn-success w-100">Start</a>
                <div class="small text-secondary text-center w-100 mt-2">0 Post(s)</div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Logs</div>
            <div class="card-body"></div>
        </div>
    </div>
</div>

@endsection
