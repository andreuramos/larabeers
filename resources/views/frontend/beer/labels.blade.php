<div class="carousel slide" data-ride="carousel" id="labelsCarousel">
    <ol class="carousel-indicators">
        @foreach($labels as $i=>$label)
            <li data-target="#labelsCarousel" data-slide-to="{{ $i }}" class="{{ $i==0?"active":"" }}"></li>
        @endforeach
    </ol>
    <div class="carousel-inner">
        @foreach($labels as $i=>$label)
            <div class="carousel-item {{ $i==0?"active":"" }}">
                {{--<img src="https://www.generationsforpeace.org/wp-content/uploads/2018/03/empty.jpg" class="d-block w-100">--}}
                <div class="bg-secondary d-block w-100" style="height:50vh"></div>
                <div class="carousel-caption d-none d-md-block">
                    <h5>{{ $label->year }}</h5>
                    <p>
                        @foreach($label->tags as $tag)
                            <span class="badge badge-secondary"><i class="fa fa-tag"></i>&nbsp;{{ $tag->text }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
        @endforeach
    </div>
    @if( $labels->count() > 1)
        <a class="carousel-control-prev" href="#labelsCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#labelsCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    @endif
</div>
