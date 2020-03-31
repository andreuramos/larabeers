@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-beer"></i> Edit Beer
                    </div>
                    <div class="card-body">
                        {{ Form::open(['action' => ['DashboardController@update_beer', $beer->id], 'files' => 'true']) }}
                            {{ Form::label('name', 'Beer Name') }}
                            {{ Form::text('name', $beer->name) }}
                        <br>
                        <p>Add a new label</p>
                            {{ Form::label('label', "Image File") }}
                            {{ Form::file('label') }}
                        <br>
                            {{ Form::label('year', 'Year') }}
                            {{ Form::text('year') }}
                        <br>
                            {{ Form::label('album', 'Album') }}
                            {{ Form::text('album') }}
                        <br>
                            {{ Form::label('page', 'Page') }}
                            {{ Form::text('page') }}
                        <br>
                            {{ Form::label('position', 'Position') }}
                            {{ Form::text('position') }}
                        <br>
                            {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                        {{ Form::close() }}
                        <a class="btn btn-secondary" href="{{ url('/beer/'. $beer->id) }}">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
