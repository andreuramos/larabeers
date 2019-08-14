@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Metrics</div>

                    <div class="card-body">
                        <div class="col-6">
                            <label>{{ __('Beers') }}</label>
                            <span>{{ $beers }}</span>
                        </div>
                        <div class="col-6">
                            <label>{{ __('Brewers') }}</label>
                            <span>{{ $brewers }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-3">
            <div class = "col-md-8">
                <div class="card">
                    <div class="card-header">Upload</div>

                    <div class="card-body">
                        {{ Form::open(['method' => "POST", 'url'=>url('/dashboard/upload-csv'), 'enctype'=>"multipart/form-data"]) }}
                        <label>{{ __('Upload') }}</label>
                        {{ Form::file('csv') }}
                        {{ Form::submit('Upload file') }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection