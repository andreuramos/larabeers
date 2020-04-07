<label for="brewer_name" class="beer-form__field-block__label">Brewer &nbsp; <i class="fa fa-industry"></i></label>
<div class="beer-form__field-block__input">
    {{ Form::text('brewer_name', $brewer ? $brewer->name : null, ['id' => 'brewer_name']) }}
    {{ Form::hidden('brewer_id', $brewer ? $brewer->id : null, ['id' => 'brewer_id']) }}
    <div class="brewer_autocomplete_list autocomplete_list hidden"></div>
</div>

<div
    class="modal fade"
    id="brewer-modal"
    role="dialog"
    aria-hidden="true"
    tabindex="-1"
>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><i class="fa fa-industry"></i>&nbsp; Create new Brewer</div>
            <div class="modal-body">the form goes here</div>
            <div class="modal-footer">
                <a class="btn btn-primary">Save</a>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function selectBrewer(id,name) {
        $("#brewer_id").val(id);
        $("#brewer_name").val(name);
        $(".brewer_autocomplete_list").addClass('hidden');
    }

    function openBrewerModal(brewer_name) {
        $("#brewer-modal").modal();
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
                    let $autocomplete_list = $(".brewer_autocomplete_list");
                    $autocomplete_list.removeClass('hidden');
                    $autocomplete_list.html('');
                    if (data.length > 0) {
                        $.each(data, function(idx, brewer) {
                            let suggestion = '<div class="autocomplete_list__item" onclick="selectBrewer(' + brewer.id + ',\'' + brewer.name+'\')" data-brewer-id="'+brewer.id+'">'+brewer.name+'</div>';
                            $autocomplete_list.append(suggestion);
                        });
                    }
                    let create_brewer_node = '<div class="autocomplete_list__item" onclick="openBrewerModal(\'' + query + '\')"><strong> Create new Brewer </strong></div>';
                    $autocomplete_list.append(create_brewer_node);
                }).fail(function(xhr, status, error){
                    console.log(status);
                })
            } else {
                $(".brewer_autocomplete_list").addClass('hidden');
            }
        });
    });
</script>
