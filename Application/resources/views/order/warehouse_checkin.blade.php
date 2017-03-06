@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/warehousecheckinform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('order_id', old('order_id', isset($order_id)?$order_id:null), ['class' => 'form-control']) !!}
            <div class="col-md-12">
                FBAforward order # : {{isset($user)? $user[0]->order_no :null}}
            </div>
            <div class="col-md-6">
                Customer Business Name : {{isset($user)? $user[0]->company_name :null}}
            </div>
            <div class="col-md-6">
                Customer Email : {{isset($user)? $user[0]->contact_email :null}}
            </div>
            {{--*/$cnt=1/*--}}
            @foreach($shipment as $key=>$shipments)
                <div>
                    <h4>Shipment # {{$key+1}} </h4>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="table-responsive no-padding">
                        <table class="table" id="list">
                            <thead>
                            <tr>
                                <th class="col-md-2"><span>Product</span></th>
                                <th class="col-md-2"><span>Qty Per Case</span></th>
                                <th class="col-md-2"><span># Of Case</span></th>
                                <th><span>Total</span></th>
                                <th><span>Shipment Type</span></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($shipment_detail as $shipment_details)
                                @if($shipment_details->shipment_id==$shipments->shipment_id)
                                    <tr>
                                        <td>@if($shipment_details->product_nick_name==''){{ $shipment_details->product_name}} @else {{$shipment_details->product_nick_name}} @endif</td>
                                        <td>{{ $shipment_details->qty_per_box}}</td>
                                        <td>{{ $shipment_details->no_boxs}}</td>
                                        <td>{{ $shipment_details->total}}</td>
                                        <td>{{ $shipment_details->shipping_name }}</td>
                                    </tr>

                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('cartoon_length', 'Cartoon Length <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_length{{$cnt}}" id="cartoon_length{{$cnt}}" class="form-control validate[required]" placeholder="Cartoon Length">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('cartoon_width', 'Cartoon Width <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_width{{$cnt}}" id="cartoon_width{{$cnt}}" class="form-control validate[required]" placeholder="Cartoon Width">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('cartoon_weight', 'Cartoon Weight <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_weight{{$cnt}}" id="cartoon_weight{{$cnt}}" class="form-control validate[required]" placeholder="Cartoon Weight">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('cartoon_height', 'Cartoon Height <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_height{{$cnt}}" id="cartoon_height{{$cnt}}" class="form-control validate[required]" placeholder="Cartoon Height">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('no_of_cartoon', '# Of Cartoon <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="no_of_cartoon{{$cnt}}" id="no_of_cartoon{{$cnt}}" class="form-control validate[required]" placeholder="# Of Cartoon">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('unit_per_cartoon', 'Unit Per Cartoon <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="unit_per_cartoon{{$cnt}}" id="unit_per_cartoon{{$cnt}}" class="form-control validate[required]" placeholder="Unit Per Cartoon">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('cartoon_condition', 'Cartoon Condition <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_condition{{$cnt}}" id="cartoon_condition{{$cnt}}" class="form-control validate[required]" placeholder="Cartoon Condition">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('images', 'Upload Image ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="images{{$cnt}}[]" id="images{{$cnt}}[]"  multiple placeholder="Upload Image">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('location', 'Warehouse Location <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                       <select name="location{{$cnt}}" id="location{{$cnt}}" class="form-control validate[required]">
                                           <option value="">Select Warehouse Location</option>
                                           <option value="1">test</option>
                                       </select>
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
