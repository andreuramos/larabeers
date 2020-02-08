@extends('layouts.app')
@section('content')

<div class="container">
    <div class="col-sm-6 col-xs-12">
        <div class="card">
            <div class="card-header">
                <i class="fab fa-google-drive"></i>
                Google Drive
            </div>
            <div class="card-body">
                <span class="text-secondary">Use Google Drive to store the images of the labels</span>
                <a class="btn btn-primary" href="{{ $auth_url }}">Connect Google Drive account</a>
                @if ($account_connected)
                    <p class="text-success"><i class="fa fa-check"></i>&nbsp;Connected</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
