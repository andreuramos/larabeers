@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>{{$tag->text}}</h1>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card w-100">
                    <div class="card-header">
                        Beers with <span class="badge badge-primary"><i class="fa fa-tag"></i> {{$tag->text}}</span>
                    </div>
                    <div class="card-body">
                        <div id="fixedBeerList" beer_ids="{{ $beer_ids }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
