<label for="brewer_name" class="beer-form__field-block__label">Brewer &nbsp; <i class="fa fa-industry"></i></label>
<div class="beer-form__field-block__input">
    {{ Form::text('autocomplete_brewer_name', $brewer ? $brewer->name : null, ['id' => 'autocomplete_brewer_name']) }}
    {{ Form::hidden('autocomplete_brewer_id', $brewer ? $brewer->id : null, ['id' => 'autocomplete_brewer_id']) }}
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
            <div class="modal-body">
                <span id="brewer_error" class="alert alert-danger hidden" role="alert"></span>
                @include('dashboard.brewer.basic-form', ['brewer' => null])
            </div>
            <script>
                $(document).ready(function() {
                    $("#submit_brewer").click(function(e) {
                        e.preventDefault();
                        $("#brewer_error").addClass('hidden');
                        $(this).html('<i class="fa fa-circle-notch fa-spin"></i>')
                        var brewer_data = {
                            '_token': "{{ csrf_token() }}",
                            'name': $("#brewer_name").val(),
                            'country': $("#brewer_country").val(),
                            'city': $("#brewer_city").val()
                        };
                        $.ajax({
                            url: '/ajax/create_brewer',
                            method: 'POST',
                            data: brewer_data,
                            success: function(data) {
                                $("#autocomplete_brewer_id").val(data.id);
                                $("#autocomplete_brewer_name").val(data.name);
                                $("#sumbit_brewer").html('Save');
                                $("#brewer_name").val('');
                                $("#brewer_country").val('');
                                $("#brewer_city").val('');
                                $("#brewer-modal").modal('toggle');
                                $(".brewer_autocomplete_list").addClass('hidden');
                            },
                            error: function(error) {
                                console.log(error);
                                $("#brewer_error").removeClass('hidden').html("Error: " + error.responseJSON.error);
                                $("#submit_brewer").html('Save')
                            }
                        });
                    });
                })
            </script>
        </div>
    </div>
</div>


<script type="text/javascript">
    function selectBrewer(id,name) {
        $("#autocomplete_brewer_id").val(id);
        $("#autocomplete_brewer_name").val(name);
        $(".brewer_autocomplete_list").addClass('hidden');
    }

    function openBrewerModal(brewer_name) {
        $("#brewer-modal").modal();
    }

    $(document).ready(function(){
        $("input[name='autocomplete_brewer_name']").keyup(function () {
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
