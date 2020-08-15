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
                        <div class="col-6">
                            <label>{{ __("Beers with picture") }}</label>
                            <span>{{ $beers_with_picture }} ({{ $beers_with_picture_percent }}%)</span>
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


        <div class="row justify-content-center mt-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <span>Last Beers</span>
                        <a href="{{ url('/brewer/new') }}" style="float:right;" class="btn btn-primary">Add Brewer</a>
                        &nbsp;
                        <a href="{{ url('/beer/new') }}" style="float:right; margin-right:1em;" class="btn btn-primary">Add Beer</a>
                    </div>
                    <div class="card-body">
                        <div id="fixedBeerList" beer_ids="{{ $last_beer_ids }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
