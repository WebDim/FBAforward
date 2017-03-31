@extends('layouts.frontend.app')

@section('title', $title)

@section('css')

@endsection

@section('content')
    <div class="row">
        {!! Form::open(['url' =>  isset($company) ? 'partnercompany/'.$company->id  :  'partnercompany', 'method' => isset($company) ? 'put' : 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}

            <div class="col-md-12">
                <h4>{{ $title }}</h4>
                <hr>
                <br/>
                <div class="form-group{{ $errors->has('delivery_company') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Delivery Company <span class="required">*</span></label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Delivery Company" class="form-control validate[required]" name="delivery_company" value="{{ old('delivery_company', isset($company) ? $company->delivery_company : null) }}">
                        </div>
                        @if ($errors->has('delivery_company'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('delivery_company') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('terminal') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Terminal <span class="required">*</span></label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Terminal" class="form-control validate[required]" name="terminal" value="{{ old('terminal', isset($company) ? $company->terminal : null) }}">
                        </div>
                        @if ($errors->has('terminal'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('terminal') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('destination') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Destination <span class="required">*</span></label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Destination" class="form-control validate[required]" name="destination" value="{{ old('destination', isset($company) ? $company->destination : null) }}">
                        </div>
                        @if ($errors->has('destination'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('destination') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-primary">Submit </button>
                </div>
            </div>
        </form>
    </div>
    </div>
@endsection

@section('js')
    {!! Html::script('assets/plugins/validationengine/languages/jquery.validationEngine-en.js') !!}
    {!! Html::script('assets/plugins/validationengine/jquery.validationEngine.js') !!}
    <script type="text/javascript">
        $(document).ready(function () {
            // Validation Engine init
            var prefix = 's2id_';
            $("form[id^='validate']").validationEngine('attach',
                {
                    promptPosition: "bottomRight", scroll: false,
                    prettySelect: true,
                    usePrefix: prefix
                });
        });
    </script>
@endsection
