<div class="preview-file-input">
    <img id="input-preview-{{ $id }}" src="#" class="hidden">
    <label for="input-file-{{ $id }}" class="label-form__image__label btn btn-secondary"><i class="fa fa-image"></i>&nbsp;Browse</label>
    {{ Form::file('label', ['id' => "input-file-".$id, 'class' => "hidden"]) }}
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#input-file-{{ $id }}").change(function() {
            var $input = $(this).get()[0];
            if ($input.files && $input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var $preview = $("#input-preview-{{ $id }}");
                    $preview.attr('src', e.target.result);
                    $preview.removeClass('hidden');

                }
                reader.readAsDataURL($input.files[0]);
            }
        });
    });
</script>
