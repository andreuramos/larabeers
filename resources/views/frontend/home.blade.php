@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header"><i class="fa fa-chart-pie"></i>&nbsp;Stats</div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($stats as $stat)
                                <div class="col-12 col-sm-6 col-md-3 justify-content-center" title="{{ $stat['name'] }}">
                                    <h3><i class="fa fa-{{ $stat['icon'] }}"></i>&nbsp;{{ $stat['value'] }}</h3>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

               @include('frontend.search', ['beers' => $beers])
            </div>
        </div>
    </div>
@endsection
