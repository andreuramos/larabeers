<div class="col-12 beer-list__beer">
    <div class="beer-list__beer__name">
        <a href="{{ url('/beer/'.$beer->id) }}">{{ $beer->name }}</a>
    </div>
    <div class="beer-list__beer__data">
        <span class="beer-list__beer__data__brewer">{{ $beer->brewers[0]->name }}</span>
        <div class="badge badge-secondary">{{ $beer->labels[0]->year }}</div>
    </div>
</div>
