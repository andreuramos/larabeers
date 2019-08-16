<div class="container">
    <span><b>{{ $beers->count() }}</b> results</span>
    <div class="list-group">
    @forelse($beers as $beer)
        <div class="list-group-item">
            @include('frontend.beer_item', ['beer'=>$beer])
        </div>
    @empty
        <span>No beers found</span>
    @endforelse
    </div>
</div>
