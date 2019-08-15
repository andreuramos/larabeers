<div class="card">
    <div class="card-header"><i class="fa fa-search"></i>&nbsp;{{ Form::text('query',null,['id'=>'search','placeholder'=>'Find Beers']) }}</div>

    <div class="card-body" id="search-results">

    </div>
</div>

{{Html::script('js/frontend/search.js')}}
