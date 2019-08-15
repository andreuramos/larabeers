<div class="card">
    <div class="card-header"><i class="fa fa-search"></i>&nbsp;Search Beers</div>

    <div class="card-body">
        {{ Form::open(['url'=>url('/find'), 'method' => "POST"]) }}
        {{ Form::text('query',null,['id'=>'search','placeholder'=>'Find Beers']) }}
        {{ Form::submit('find!') }}
        {{ Form::close() }}
    </div>
</div>

{{Html::script('js/frontend/search.js')}}
