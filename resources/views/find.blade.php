@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ count($beers) }} results for <b>{{$query}}</b></div>

                    <div class="card-body">
                        @forelse($beers as $beer)
                            @include('beer_list',['beer'=>$beer])
                        @empty
                            no beers found, try searching another...<br>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        {{ Form::open(['url'=>url('/find'), 'method' => "POST"]) }}
                            {{ Form::text('query') }}
                            {{ Form::submit('find!') }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
