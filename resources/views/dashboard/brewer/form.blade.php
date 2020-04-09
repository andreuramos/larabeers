    <div class="field-block">
        <div class="field-block__label">
            <label for="brewer_name">Name</label>
        </div>
        <div class="field-block__input">
            <input type="text" id="brewer_name" name="brewer_name" value="{{ $brewer ? $brewer->name : null }}">
        </div>
    </div>

    <div class="field-block">
        <div class="field-block__label">
            <label for="brewer_country">Country</label>
        </div>
        <div class="field-block__input">
            <input type="text" id="brewer_country" name="brewer_country">
        </div>
    </div>

    <div class="field-block">
        <div class="field-block__label">
            <label for="brewer_city">City</label>
        </div>
        <div class="field-block__input">
            <input type="text" id="brewer_city" name="brewer_city">
        </div>
    </div>
    <a class="btn btn-primary col-12" style="color: white" id="submit_brewer">Save</a>
