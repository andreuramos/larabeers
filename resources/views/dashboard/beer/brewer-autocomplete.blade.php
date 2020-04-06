{{ Form::label('brewer_autocomplete', "Brewer(s)") }}
{{ Form::input('brewer_autocomplete', null) }}
{{ Form::hidden('brewer_ids', implode(',',array_keys($brewers))) }}
@foreach($brewers as $id => $brewer)
    <div class="badge badge-secondary">
        <i class="fa fa-industry"></i>
        &nbsp;
        <span>{{$brewer}}</span>
        <i class="fa fa-times remove-brewer" style="cursor: pointer" data-brewer="{{ $id }}"></i>
    </div>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='brewer_autocomplete']").keyup(function () {
            let query = $(this).val();
            console.log(query);
            console.log(query.length)
            if (query.length > 2) {
                $.ajax({
                    url: '/ajax/brewer_autocomplete',
                    type: "GET",
                    data: {
                        query: query
                    }
                }).done(function(data){
                    $("#brewer_suggestions").html(data);
                }).fail(function(xhr, status, error){
                    console.log(status);
                })
            }
        });


       $('.remove-brewer').click(function() {
           var brewer_id = $(this).attr('data-brewer');
           var $brewer_input = $("input[name='brewer_ids']");
           if ($brewer_input.val().split(',').length > 1) {
               var current_brewers = $brewer_input.val().split(',');
               var brewer_to_delete_position = current_brewers.indexOf(brewer_id);
               current_brewers.splice(brewer_to_delete_position,1);
               $brewer_input.val(current_brewers);
               $(this).parent().remove();
           }
       })
    });
</script>
