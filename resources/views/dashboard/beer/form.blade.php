@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fa fa-beer"></i> Edit Beer
                    </div>
                    <div class="card-body">
                        <div class="beer-form">
                            {{ Form::open(['action' => ['DashboardController@update_beer', $beer->id], 'files' => 'true']) }}
                                {{ Form::label('name', 'Beer Name', ['class' => 'beer-form__label']) }}
                                {{ Form::text('name', $beer->name) }}

                            <p>Add a new label</p>
                                {{ Form::label('label', "Image File", ['class' => 'beer-form__label']) }}
                                {{ Form::file('label') }}
                            <br>
                                {{ Form::label('year', 'Year', ['class' => 'beer-form__label']) }}
                                {{ Form::text('year') }}
                            <br>
                                {{ Form::label('album', 'Album', ['class' => 'beer-form__label']) }}
                                {{ Form::text('album') }}
                            <br>
                                {{ Form::label('page', 'Page', ['class' => 'beer-form__label']) }}
                                {{ Form::text('page') }}
                            <br>
                                {{ Form::label('position', 'Position', ['class' => 'beer-form__label']) }}
                                {{ Form::text('position') }}
                            <br>
                                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                            {{ Form::close() }}
                            <a class="btn btn-secondary" href="{{ url('/beer/'. $beer->id) }}">Cancel</a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-sticky-note"></i> &nbsp; Labels
                    </div>
                    <div class="card-body">
                        <div class="beer-form__labels">
                        @foreach($beer->labels as $label)
                            @include('dashboard.beer.label', ['label' => $label])
                        @endforeach
                        @include('dashboard.beer.label', ['label' => null])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
