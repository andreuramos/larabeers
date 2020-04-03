<div class="beer-form__labels__label">
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
