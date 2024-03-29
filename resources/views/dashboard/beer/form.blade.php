@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6 col-sm-10">
                                <i class="fa fa-beer"></i> Beer Data
                            </div>
                            <div class="col-6 col-sm-2">
                                @if ($beer->id)
                                    <a class="btn btn-outline-secondary w-100" href="{{ url('/beer/'. $beer->id) }}">Back</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="beer-form">
                            {{ Form::open([
                                'action' => $beer->id ? ['BeerController@updateBeer', $beer->id] : ['BeerController@createBeer'],
                                'files' => 'true',
                                'autocomplete' => 'off']
                            ) }}
                                <div class="beer-form__field-block">
                                    <label for="name" class="beer-form__field-block__label">
                                        Beer Name &nbsp; <i class="fa fa-beer"></i>
                                    </label>
                                    {{ Form::text('name', $beer->name) }}
                                </div>

                                <div class="beer-form__field-block">
                                    @include('dashboard.beer.brewer-autocomplete', ['brewer' => $beer->brewers[0]])
                                </div>

                                <div class="beer-form__field-block">
                                    @include('dashboard.beer.style-autocomplete', ['style' => $beer->style])
                                </div>

                                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-sticky-note"></i> &nbsp; Labels
                    </div>
                    <div class="card-body">
                        <div class="beer-form__labels">
                        @foreach($beer->labels as $label)
                            @include('dashboard.beer.label', ['label' => $label, 'beer_id' => $beer->id])
                        @endforeach
                        @if ($beer->id)
                            @include('dashboard.beer.label', ['label' => null, 'beer_id' => $beer->id])
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
