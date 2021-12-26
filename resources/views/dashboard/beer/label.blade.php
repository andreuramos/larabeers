<div
    class="beer-form__labels__label"
    data-target="#label-modal-{{ $label ? $label->id : 'new' }}"
    data-toggle="modal"
>
    @if($label)
        <img src="{{ $label->sticker ? $label->sticker->url : null }}"/>
    @else
        <img src="{{ URL::asset('img/label-template.jpg') }}">
    @endif
    <div class="beer-form__labels__label__data">
        @if($label)
        <div class="badge badge-secondary">{{ $label->year }}</div>
        @endif
    </div>

    <div class="beer-form__labels__label__hover">
        <div class="beer-form__labels__label__hover__icon">
        @if($label)
            <i class="fa fa-pencil"></i>
        @else
            <i class="fa fa-plus"></i>
        @endif
        </div>
    </div>
</div>

<div
    class="modal fade"
    id="label-modal-{{ $label ? $label->id : 'new' }}"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
    tabindex="-1"
>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{ $label ? "Edit Label" : "New Label" }}
            </div>
            {{ Form::open(['action' => $label ? ['BeerController@updateLabel', $label->id] : ['BeerController@addLabelToBeer', $beer_id], 'enctype' =>"multipart/form-data" ]) }}
            <div class="modal-body">
                <div class="label-form container">
                    <div class="row">
                        {{ Form::hidden('label_id', $label ? $label->id : null) }}
                        {{ Form::hidden('beer_id', $beer_id) }}
                        <div class="label-form__image col-12 col-sm-6">
                            @include('dashboard.beer.image-input', ['id' => "input" . ($label ? $label->id : 'new')])
                        </div>

                        <div class="label-form__data col-12 col-sm-6">
                            <span class="label-form__data__label">Year&nbsp;<i class="fa fa-calendar-alt"></i></span>
                            {{ Form::input('number', 'year', $label ? $label->year : null) }}
                            <span class="label-form__data__label">Album&nbsp;<i class="fa fa-book"></i></span>
                            {{ Form::input('number', 'album', $label ? $label->album : null) }}
                            <span class="label-form__data__label">Page&nbsp;<i class="fa fa-book-open"></i></span>
                            {{ Form::input('number', 'page', $label ? $label->page : null) }}
                            <span class="label-form__data__label">Position&nbsp;<i class="fa fa-th"></i></span>
                            {{ Form::input('number', 'position', $label ? $label->position : null) }}
                            <br>
                        </div>

                        <div class="col-12">
                            @include('dashboard.beer.tag-autocomplete', [
                                'tags' => $label ? $label->tags : [],
                                'label_id' => $label ? $label->id : 'new'
                                ])
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                @if ($label)
                    <a class="btn btn-danger" href="{{url('/beer/' . $beer_id . '/label/' . $label->id . '/delete')}}">Delete</a>
                @endif
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
