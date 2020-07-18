@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>{{$brewer->name}}</h1>
        <div class="row mt-4">
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-info"></i>
                        @if(Auth::check())
                            <a class="btn btn-primary" style="float:right" href="{{ url('brewer/'.$brewer->id.'/edit') }}">
                                <i class="fa fa-pencil"></i>
                                Editar
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @include('frontend.brewer.info', ['brewer' => $brewer])
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card w-100">
                    <div class="card-header">
                        Brewed by {{$brewer->name}}
                    </div>
                    <div class="card-body">
                        <div id="fixedBeerList" beer_ids="{{ $beer_ids }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
