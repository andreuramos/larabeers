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
                        {{ Form::open() }}
                        @include('dashboard.brewer.basic-form', ['brewer' => $brewer])
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
