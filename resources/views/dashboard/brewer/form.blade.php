@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 col-sm-10">
                                <i class="fa fa-industry"></i> Brewer Data
                            </div>
                            <div class="col-6 col-sm-2">
                                @if ($brewer->id)
                                    <a class="btn btn-outline-secondary w-100" href="{{ url('/brewer/'. $brewer->id) }}">Back</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ Form::open([
                            'action' => $brewer->id ? ['BrewerController@updateBrewer', $brewer->id] : ['BrewerController@createBrewer'],
                            'files' => 'true',
                            'autocomplete' => 'off'
                        ]) }}
                        <div class="brewer-form">
                        @include('dashboard.brewer.basic-form', ['brewer' => $brewer])
                            <div class="field-block">
                                <div class="field-block__label">
                                    <label for="brewer-address">Address</label>
                                </div>
                                <div class="field-block__input">
                                    <input id="brewer-address" name="brewer_address">
                                </div>
                                <div id="brewer-map"></div>
                                <input type="hidden" name="brewer-lat">
                                <input type="hidden" name="brewer-lng">
                            </div>
                        </div>
                        <input
                            type="submit"
                            class="btn btn-primary col-12"
                            id="submit_brewer"
                            value="Save">
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function initMap() {

        }
    </script>
@endsection
