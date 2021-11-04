<div class="card">
    <div class="card-header">
        <div class="row">
            <h3 class="col-12">
                <i class="fa fa-info"></i>
                @if(Auth::user())
                    <a href="{{ url('/beer/'.$beer->id.'/edit') }}" class="btn btn-outline-primary float-right m-1"><i class="fa fa-pencil"></i>&nbsp;Edit</a>
                    <a href="{{ url('/beer/'.$beer->id.'/delete') }}" class="btn btn-outline-danger float-right m-1"><i class="fa fa-times"></i> Delete</a>
                @endif
            </h3>
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
                {{ $beer->style }}
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
