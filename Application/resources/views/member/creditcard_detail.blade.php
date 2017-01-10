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
                        {!! Form::label('credit_card_number', 'Credit Card Number *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('credit_card_number', old('credit_card_number'), ['class' => 'form-control validate[required]', 'placeholder'=>'Credit Card Number']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('credit_card_type', 'Credit Card Type *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                  {!! Form::select('credit_card_type', array_add($card_type, '','Please Select'), old('credit_card_type'), ['class' => 'form-control select2 validate[required]']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('expire_card', 'Expire Card *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('expire_card', old('expire_card'), ['id' => 'expire_card', 'class' => 'form-control validate[required]', 'placeholder'=>'Expire Card']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('cvv', 'CVV *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('cvv', old('cvv'), ['class' => 'form-control validate[required]', 'placeholder'=>'CVV']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('first_name', 'First Name *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('first_name', old('first_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'First Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('last_name', 'Last Name *', ['class' => 'control-label col-md-4']) !!}
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
    {!! Html::style('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css') !!}
    {!! Html::script("http://code.jquery.com/jquery-1.9.1.js") !!}
    {!! Html::script("http://code.jquery.com/ui/1.11.0/jquery-ui.js") !!}
    {!! Html::script("http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js") !!}
    {!! Html::script("http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js") !!}

    <script type="text/javascript">
        $(document).ready(function () {

            $('#expire_card').datepicker( {
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'm yy',
                onClose: function(dateText, inst) {
                    $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                }
            });
        });

    </script>
@endsection
