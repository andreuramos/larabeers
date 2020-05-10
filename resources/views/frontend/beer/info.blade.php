<div class="card">
    <div class="card-header">
        <div class="row">
        <h3 class="col-10"><i class="fa fa-info"></i></h3>
        @if(Auth::user())
                <a href="{{ url('/beer/'.$beer->id.'/edit') }}" class="btn btn-outline-primary"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
        @endif
        </div>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item">
                <i class="fa fa-industry"></i>&nbsp;
                <a href="{{ url('/brewer/'.$beer->brewers[0]->id) }}">{{ $beer->brewers[0]->name }}</a>
            </li>
            <li class="list-group-item">
                <i class="fa fa-globe-europe"></i>&nbsp;
                {{ $beer->brewers[0]->city->country->name }}&nbsp;/&nbsp;
                {{ $beer->brewers[0]->city->name }}
            </li>
            <li class="list-group-item">
                <i class="fa fa-font"></i>&nbsp;
                {{ $beer->style->name }}
            </li>
            @if (count($beer->labels))
            <li class="list-group-item">
                <i class="fa fa-book" title="Album"></i>
                {{ $beer->labels[0]->album }}
                <i class="fa fa-book-open" title="Page"></i>
                {{ $beer->labels[0]->page }}
                <i class="fa fa-th" title="Position"></i>
                {{ $beer->labels[0]->position }}
            </li>
            @endif
        </ul>
    </div>
</div>
