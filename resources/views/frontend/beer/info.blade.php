<div class="card">
    <div class="card-header">
        <h3><i class="fa fa-info"></i></h3>
    </div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item">
                <i class="fa fa-industry"></i>&nbsp;{{ $beer->brewers->first()->name }}
            </li>
            <li class="list-group-item">
                <i class="fa fa-globe-europe"></i>&nbsp;
                {{ $beer->brewers->first()->country }}&nbsp;/&nbsp;
                {{ $beer->brewers->first()->city }}
            </li>
            <li class="list-group-item">
                <i class="fa fa-font"></i>&nbsp;
                {{ $beer->type }}
            </li>
        </ul>
    </div>
</div>
