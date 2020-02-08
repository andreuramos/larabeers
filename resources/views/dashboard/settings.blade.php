@extends('layouts.app')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            Google Drive Connection
        </div>
        <div class="card-body">
            <a href="{{ $auth_url }}">Connect Google Drive</a>
        </div>
    </div>
</div>
@endsection
