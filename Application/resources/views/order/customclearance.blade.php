@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-10">
                <h2 class="page-head-line">{{$title}}</h2>
            </div>
            <div class="col-md-2 ">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <br>
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
                    <div class="col-md-6"><h4>Shipment  </h4></div>
                    <div class="col-md-6"><h4>{{$shipments->shipping_name}}</h4></div>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('form_3461', 'Form 3461<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="form_3461{{$cnt}}" id="form_3461{{$cnt}}"  placeholder="Form 3461 " class="validate[required]">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('form_7501', 'Form 7501<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="form_7501{{$cnt}}" id="form_7501{{$cnt}}"  placeholder="Form 7501 " class="validate[required]">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('delivery_order', 'Delivery Order<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="delivery_order{{$cnt}}" id="delivery_order{{$cnt}}"  placeholder="Delivery Order" class="validate[required]">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('abi_note', 'ABI notes <span class="required"></span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="abi_note{{$cnt}}" id="abi_note{{$cnt}}"  placeholder="ABI notes" class="">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('custom_duty', 'Custom Duties<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="custom_duty{{$cnt}}" id="custom_duty{{$cnt}}"  class="form-control validate[required]" placeholder="Custom Duties ">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('terminal_fee', 'Terminal Fees<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="terminal_fee{{$cnt}}" id="terminal_fee{{$cnt}}"  class="form-control validate[required]" placeholder="Terminal Fees " >
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('addition_service', 'Addition Services<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="checkbox" class="validate[groupRequired[charge], minCheckbox[1]]" name="addition_service{{$cnt}}[]" id="addition_service{{$cnt}}_1" value="1">FDA Clearance<br>
                                        <input type="checkbox" class="validate[groupRequired[charge], minCheckbox[1]]" name="addition_service{{$cnt}}[]" id="addition_service{{$cnt}}_2" value="2">Lacey Act<br>
                                        <input type="checkbox"  class="validate[groupRequired[charge], minCheckbox[1]]"name="addition_service{{$cnt}}[]" id="addition_service{{$cnt}}_3" value="3">OGA/PGA
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
