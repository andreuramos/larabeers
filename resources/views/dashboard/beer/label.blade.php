<div
    class="beer-form__labels__label"
    data-target="#label-modal-{{ $label ? $label->id : 'new' }}"
    data-toggle="modal"
>
    @if($label)
        <img src="{{ $label->path() }}"/>
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
                edit label
            </div>
            {{ Form::open(['action' => $label ? ['DashboardController@update_label', $label->id] : ['DashboardController@add_label_to_beer', $beer_id] ]) }}
            <div class="modal-body">
                <div class="label-form container">
                    <div class="row">
                        <div class="label-form__image col-xs-12 col-sm-6">
                            {{ Form::hidden('label_id', $label ? $label->id : null) }}
                            {{ Form::label('label', "Image File", ['class' => 'beer-form__label']) }}
                            {{ Form::file('label') }}
                        </div>
                        <div class="label-form__data col-xs-12 col-sm-6">
                            <br>
                            {{ Form::label('year', 'Year', ['class' => 'beer-form__label']) }}
                            {{ Form::text('year', $label ? $label->year : null) }}
                            <br>
                            {{ Form::label('album', 'Album', ['class' => 'beer-form__label']) }}
                            {{ Form::text('album', $label ? $label->album : null) }}
                            <br>
                            {{ Form::label('page', 'Page', ['class' => 'beer-form__label']) }}
                            {{ Form::text('page', $label ? $label->page : null) }}
                            <br>
                            {{ Form::label('position', 'Position', ['class' => 'beer-form__label']) }}
                            {{ Form::text('position', $label ? $label->position : null) }}
                            <br>
                        </div>
                        <div class="label-form__tags col-12">
                            {{ Form::label('tags', "Tags") }}
                            {{ Form::text('tags', "") }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save') }}
            </div>
            {{ Form::close() }}
        </div>
    </div>

</div>
