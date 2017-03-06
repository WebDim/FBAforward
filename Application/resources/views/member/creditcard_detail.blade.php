@extends('layouts.frontend.app')

@section('title', 'Credit Card Details')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Credit Card Details</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  '/creditcard_detail', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('credit_card_number', 'Credit Card Number <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('credit_card_number', old('credit_card_number'), ['class' => 'form-control validate[required, custom[creditCard]]', 'placeholder'=>'Credit Card Number']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('credit_card_type', 'Credit Card Type <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                  {!! Form::select('credit_card_type', array_add($card_type, '','Please Select'), old('credit_card_type'), ['class' => 'form-control select2 validate[required]']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('expire_card', 'Expire Card <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('expire_card', old('expire_card'), ['id' => 'expire_card', 'class' => 'form-control validate[required]', 'placeholder'=>'Expire Card']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('cvv', 'CVV <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('cvv', old('cvv'), ['class' => 'form-control validate[required, custom[maxSize[3]]]', 'placeholder'=>'CVV']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('first_name', 'First Name <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('first_name', old('first_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'First Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('last_name', 'Last Name <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('last_name', old('last_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Last Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {!! Form::submit('Add Credit Card Info', ['class'=>'btn btn-primary']) !!}
                        </div>
                    </div>
                </div><!-- .col-md-6 -->
            </div><!-- .row -->
            {!! Form::close() !!}
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('#expire_card').datepicker( {
                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months"
            });
        });

    </script>
@endsection
