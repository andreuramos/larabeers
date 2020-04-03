<div class="col-12 beer-list__beer">
    <div class="beer-list__beer__image">
        <img src="{{ $beer->labels[0]->sticker->url ?? URL::asset('img/label-template.jpg') }}"/>
    </div>
    <div class="beer-list__beer__data">
        <div class="beer-list__beer__name">
            <a href="{{ url('/beer/'.$beer->id) }}">{{ $beer->name }}</a>
        </div>
        <span class="beer-list__beer__data__brewer">{{ $beer->brewers[0]->name }}</span>
        <div class="badge badge-secondary">{{ $beer->labels[0]->year }}</div>
    </div>
</div>
