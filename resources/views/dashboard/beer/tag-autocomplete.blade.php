<div class="label-form__tags col-12" data-label="{{ $label_id }}">
    <label class="label-form__tags__label" for="tags">Tags</label>
    {{ Form::text('tags', "", ['id' => "label-$label_id-tag-autocomplete"]) }}
    <div class="tag-autocomplete-list autocomplete_list hidden"></div>
    {{ Form::hidden('tag_names', implode('|', $tags), ['id' => "label-$label_id-tag-names"]) }}
</div>
<div class="label-form__tag-list-{{$label_id}} col-12">
    @foreach($tags as $tag)
        <div class="label-badge badge badge-primary" data-tag="{{ $tag->text }}">
            <i class="fa fa-tag"></i>&nbsp;&nbsp;
            {{ $tag->text }}&nbsp;&nbsp;
            <a href="#" onclick="removeTag('{{ $tag->text }}','{{ $label_id }}')"><i class="fa fa-times"></i></a>
        </div>
    @endforeach
</div>
<script>
    function buildBadge(name, label_id) {
        return '<div class="label-badge badge badge-primary" data-tag="' + name + '">' +
        '<i class="fa fa-tag"></i>&nbsp;' + name +
        '&nbsp;<a onclick="removeTag(\'' + name + '\',\'' + label_id + '\')" href="#"><i class="fa fa-times"></i></a>' +
        '</div>';
    }

    function removeTag(name, label_id) { // Vue, come help me plz
        let $tags_input = $("#label-" + label_id + "-tag-names");
        let tags = $tags_input.val().split('|');
        tags.splice(tags.indexOf(name),1);
        $tags_input.val(tags.join('|'));
        $('.label-form__tag-list-' + label_id + ' div[data-tag="' + name + '"]').remove();
    }

    function selectTag(name, label_id) {
        let $tags_input = $("#label-" + label_id + "-tag-names");
        let tags = $tags_input.val().split('|');
        tags.push(name);
        $tags_input.val(tags.join('|'));
        $(".label-form__tag-list-{{$label_id}}").append(buildBadge(name, label_id));
        $(".tag-autocomplete-list").addClass('hidden');
        $("#label-{{$label_id}}-tag-autocomplete").val("");
    }

    $(document).ready(function(){
        $("#label-{{$label_id}}-tag-autocomplete").keyup(function(){
            var tag_text = $(this).val();
            if (tag_text.length > 2) {
                $.ajax({
                    url: '/ajax/tag_autocomplete?query=' + tag_text,
                    method: 'GET',
                    success: function(data) {
                        let $autocomplete_list = $("div[data-label='{{$label_id}}']").children('.autocomplete_list');
                        $autocomplete_list.removeClass('hidden');
                        $autocomplete_list.html("");
                        if (data.length > 0) {
                            $.each(data, function(idx, item) {
                                let suggestion = '<div class="autocomplete_list__item" onclick="selectTag(\'' + item + '\',\'{{$label_id}}\')">' + item + '</div>';
                                $autocomplete_list.append(suggestion);
                            })
                        }
                    }
                });
            }
        });
        $("#label-{{$label_id}}-tag-autocomplete").keydown(function (e) {
            var tag_text = $(this).val();
            if (e.keyCode == 13 && tag_text.length) { // enter
                e.preventDefault();
                var tags = $("#label-{{$label_id}}-tag-names").val().split('|');
                if (!tags.includes(tag_text)) {
                    tags.push(tag_text);
                    $("#label-{{$label_id}}-tag-names").val(tags.join('|'));
                    let badge = buildBadge(tag_text, {{$label_id}});
                    $(this).parent().siblings('.label-form__tag-list-{{$label_id}}').append(badge)
                }
                $(this).val("");
            }
        })
    })
</script>
