@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Controls</div>
            <div class="card-body">
                <a href="{{ url('/run') }}" class="btn btn-lg btn-success w-100">Run</a>
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
