<div class="card">
    <div class="card-header"><i class="fa fa-search"></i>&nbsp;{{ Form::text('query',null,['id'=>'search','placeholder'=>'Find Beers']) }}</div>

    <div class="card-body px-0 px-md-2" id="search-results">
        @include('frontend.beer_list', ['beers' => $beers])
    </div>
</div>

{{Html::script('js/frontend/search.js')}}
