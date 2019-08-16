<div class="col-12">
    <a href="{{ url('/beer/'.$beer->id) }}">{{ $beer->name }}</a>
    <span class="font-italic text-secondary" style="font-size:small">{{ $beer->brewers->first()->name }}</span>
</div>
