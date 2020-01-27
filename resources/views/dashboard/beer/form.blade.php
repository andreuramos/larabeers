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
                            {{ Form::file('label') }}
                            {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
