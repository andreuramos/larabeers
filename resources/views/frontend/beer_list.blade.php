<div class="container">
    @forelse($beers as $beer)
        @include('frontend.beer_item', ['beer'=>$beer])
    @empty
        <span>No beers found</span>
    @endforelse
</div>
