<div class="form-group">
    {!! Form::label('company_state', 'State *', ['class' => 'control-label col-md-3']) !!}
    <div class="col-md-9">
        <div class="input-group">
            <span class="input-group-addon"></span>
            {!! Form::select('company_state', array_add($states, '','Please Select'), old('state', !empty($user_info) ? $user_info[0]->company_state: null), ['class' => 'form-control select2 validate[required]']) !!}
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    //Initialize Select2 Elements
    $(".select2").select2({
    placeholder: "Please Select",
    allowClear: true
    });
});
</script>