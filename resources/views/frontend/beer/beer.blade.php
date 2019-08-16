@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $beer->name }}</h1>
        <div class="row">
            <div class="col-12 col-md-6">
                @include('frontend.beer.labels',['labels' => $beer->labels])
            </div>
            <div class="col-12 col-md-6">
                @include('frontend.beer.info', ['beer'=>$beer])
            </div>
        </div>

    </div>


@endsection
