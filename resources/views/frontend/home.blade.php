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
                                <div class="col-6 col-md-3 justify-content-center" title="{{ $stat['name'] }}">
                                    @if($stat['url']) <a href="{{$stat['url']}}"> @endif
                                    <h3><i class="fa fa-{{ $stat['icon'] }}"></i>&nbsp;{{ $stat['value'] }}</h3>
                                        @if($stat['url']) </a> @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

               @include('frontend.search', ['beers' => $beers])

                <div id="searchableBeerList"></div>
            </div>
        </div>
    </div>
@endsection
