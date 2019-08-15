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
                                <div class="col-12 col-sm-6 col-md-3 justify-content-center">
                                    <h1>{{ $stat['value'] }}</h1>
                                    <i class="fa fa-{{ $stat['icon'] }}"></i>
                                    <span>{{ $stat['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

               @include('frontend.search')
            </div>
        </div>
    </div>
@endsection
