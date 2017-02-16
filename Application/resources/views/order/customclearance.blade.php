@extends('layouts.frontend.app')
@section('title', 'Custom Clearance Form')
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/customclearanceform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('order_id', old('order_id', isset($order_id)?$order_id:null), ['class' => 'form-control']) !!}
            <div class="col-md-6">
                FBAforward order # : {{isset($user)? $user[0]->order_no :null}}
            </div>
            <div class="col-md-6">
                Customer Email : {{isset($user)? $user[0]->contact_email :null}}
            </div>
            {{--*/$cnt=1/*--}}
            @foreach($shipment as $key=>$shipments)
                <div>
                    <div class="col-md-6"><h4>Shipment # {{$key+1}} </h4></div>
                    <div class="col-md-6"><h4>{{$shipments->shipping_name}}</h4></div>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('form_3461', 'Form 3461 ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="form_3461{{$cnt}}" id="form_3461{{$cnt}}"  placeholder="Form 3461 ">
                                    </div>
                                </div>
                                {!! Form::label('form_7501', 'Form 7501 ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="form_7501{{$cnt}}" id="form_7501{{$cnt}}"  placeholder="Form 7501 ">
                                    </div>
                                </div>
                                {!! Form::label('delivery_order', 'Delivery Order ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="delivery_order{{$cnt}}" id="delivery_order{{$cnt}}"  placeholder="Delivery Order">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('custom_duty', 'Custom Duties ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="custom_duty{{$cnt}}" id="custom_duty{{$cnt}}"  class="form-control" placeholder="Custom Duties ">
                                    </div>
                                </div>
                                {!! Form::label('addition_service', 'Addition Services ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="checkbox" name="addition_service{{$cnt}}_1" id="addition_service{{$cnt}}_1" value="1">FDA Clearance<br>
                                        <input type="checkbox" name="addition_service{{$cnt}}_2" id="addition_service{{$cnt}}_2" value="2">Lacey Act<br>
                                        <input type="checkbox" name="addition_service{{$cnt}}_3" id="addition_service{{$cnt}}_3" value="3">OGA/PGA
                                    </div>
                                </div>
                                {!! Form::label('terminal_fee', 'Terminal Fees ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="terminal_fee{{$cnt}}" id="terminal_fee{{$cnt}}"  class="form-control" placeholder="Terminal Fees ">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{--*/$cnt++/*--}}
            @endforeach
            <input type="hidden" name="count" id="count" value="{{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        {!! Form::submit('  Submit   ', ['class'=>'btn btn-primary', ]) !!}
                    </div>
                </div>
            </div>
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
@endsection
