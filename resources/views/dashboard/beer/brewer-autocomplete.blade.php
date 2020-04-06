{{ Form::label('brewer_name', "Brewer", ['class' => 'beer-form__label']) }}
{{ Form::text('brewer_name', $brewer ? $brewer->name : null) }}
{{ Form::hidden('brewer_id', $brewer ? $brewer->id : null) }}
<div class="brewer_autocomplete_list hidden">

</div>


<script type="text/javascript">
    function selectBrewer(id,name) {
        $("#brewer_id").val(id);
        $("#brewer_name").val(name);
        $(".brewer_autocomplete_list").addClass('hidden');
    }

    $(document).ready(function(){
        $("input[name='brewer_name']").keyup(function () {
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: '/ajax/brewer_autocomplete',
                    type: "GET",
                    data: {
                        query: query
                    }
                }).done(function(data){
                    console.log(data);
                    let $autocomplete_list = $(".brewer_autocomplete_list");
                    $autocomplete_list.removeClass('hidden');
                    $autocomplete_list.html('');
                    if (data.length === 1) {
                        selectBrewer(data[0].id, data[0].name);
                        $autocomplete_list.addClass('hidden');
                        console.log($(this));
                        $("input[name='brewer_name']").blur();
                    } else if (data.length > 1) {
                        $.each(data, function(idx, brewer) {
                            let suggestion = '<div class="brewer_autocomplete_list__item" onclick="selectBrewer(' + brewer.id + ',\'' + brewer.name+'\')" data-brewer-id="'+brewer.id+'">'+brewer.name+'</div>';
                            $autocomplete_list.append(suggestion);
                        });
                    } else {
                        alert("Open new brewer modal");
                    }
                }).fail(function(xhr, status, error){
                    console.log(status);
                })
            } else {
                $(".brewer_autocomplete_list").addClass('hidden');
            }
        });
        $("input[name='brewer_name']").focusout(function() {
            let $suggestions = $(".brewer_autocomplete_list");
            if (!$suggestions.hasClass('hidden')) {
                let $first_suggestion = $(".brewer_autocomplete_list__item").first();
                let brewer_id = $first_suggestion.attr('data-brewer-id');
                let brewer_name = $first_suggestion.html();
                selectBrewer(brewer_id, brewer_name);
            }
        })
    });
</script>
