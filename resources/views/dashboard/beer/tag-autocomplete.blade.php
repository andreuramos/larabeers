<div class="label-form__tags col-12">
    <label class="label-form__tags__label" for="tags">Tags</label>
    {{ Form::text('tags', "", ['id' => "label-tag-autocomplete"]) }}
    {{ Form::hidden('tag_names', implode('|', $tags), ['id' => 'label-tag-names']) }}
</div>
<div class="label-form__tag-list col-12">
    @foreach($tags as $tag)
        <div class="badge badge-primary" data-tag="{{ $tag->text }}">
            <i class="fa fa-tag"></i>&nbsp;
            {{ $tag->text }}&nbsp;
            <a href="#" onclick="removeTag('{{ $tag->text }}')"><i class="fa fa-times"></i></a>
        </div>
    @endforeach
</div>
<script>
    function removeTag(name) {
        let tags = $("#label-tag-names").val().split('|');
        tags.splice(tags.indexOf(name),1);
        $("#label-tag-names").val(tags.join('|'));
        $('div[data-tag="' + name + '"]').remove();
    }
    $(document).ready(function(){
        $("#label-tag-autocomplete").keyup(function(){
            var tag_text = $(this).val();
            if (tag_text.length > 2) {
                $.ajax({
                    url: '/ajax/tag_autocomplete?query=' + tag_text,
                    success: function(data) {

                    }
                });
            }
        });
        $("#label-tag-autocomplete").keydown(function (e) {
            var tag_text = $(this).val();
            if (e.keyCode == 13 && tag_text.length) { // enter
                e.preventDefault();
                var tags = $("#label-tag-names").val().split('|');
                if (!tags.includes(tag_text)) {
                    tags.push(tag_text);
                    $("#label-tag-names").val(tags.join('|'));
                    let badge = '<div class="label-badge badge badge-primary" data-tag="' + tag_text + '">' +
                        '<i class="fa fa-tag"></i>&nbsp;' + tag_text +
                        '<a onclick="removeTag(\'' + tag_text + '\')" href="#"><i class="fa fa-times"></i></a>' +
                        '</div>';
                    $(this).parent().siblings('.label-form__tag-list').append(badge)
                }
                $(this).val("");
            }
        })
    })
</script>
