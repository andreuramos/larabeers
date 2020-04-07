<label for="beer_style" class="beer-form__field-block__label">Style &nbsp;<i class="fa fa-font"></i></label>
{{ Form::text('beer_style', $style) }}
<div class="style-autocomplete-list autocomplete_list hidden"></div>

<script type="text/javascript">
    // TODO: refactor component to use same script than brewer-autocomplete if possible
    function selectStyle(name) {
        $("input[name='beer_style']").val(name);
        $(".style-autocomplete-list").addClass('hidden');
    }

    $(document).ready(function () {
        $("input[name='beer_style']").keyup(function () {
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: '/ajax/style_autocomplete',
                    type: "GET",
                    data: {
                        query: query
                    }
                }).done(function (data) {
                    let $autocomplete_list = $(".style-autocomplete-list");
                    $autocomplete_list.removeClass('hidden');
                    $autocomplete_list.html('');
                    console.log(data);
                    if (data.length > 0) {
                        $.each(data, function (idx, style) {
                            let suggestion = '<div class="autocomplete_list__item" onclick="selectStyle(\'' + style + '\')">' + style + '</div>';
                            $autocomplete_list.append(suggestion);
                        });
                    }
                }).fail(function (xhr, status, error) {
                    console.log(status);
                })
            } else {
                $(".brewer_autocomplete_list").addClass('hidden');
            }
        });
    });
</script>
