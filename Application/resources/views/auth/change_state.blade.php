<div class="form-group{{ $errors->has('company_state') ? ' has-error' : '' }}">
    <label class="col-md-2 control-label">State *</label>
    <div class="col-md-7">
        <div class="input-group">
            <span class="input-group-addon"></span>
            <select name="company_state" class="form-control">
                <option value="">Select State</option>
                @foreach ($states as $key=>$state)
                    <option value="{{ $key }}">  {{ $state }}</option>
                @endforeach
            </select>
        </div>
        @if ($errors->has('company_state'))
            <span class="help-block">
                <strong>{{ $errors->first('company_state') }}</strong>
            </span>
        @endif
    </div>
</div>
