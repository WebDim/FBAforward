@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/shippingquoteform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
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
                            <th><span>Supplier Details</span></th>
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
                                    <td>
                                        @foreach($supplier as $suppliers)
                                            @if($suppliers->supplier_id==$shipment_details->supplier_id)
                                                {{ $suppliers->company_name}}
                                                <br>
                                                {{$suppliers->email}}
                                                <br>
                                                {{$suppliers->phone_number }}
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>

                            @endif
                        @endforeach
                       </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('shipping_port', 'Shipping Port <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="shipping_port{{$cnt}}" id="shipping_port{{$cnt}}" class="form-control validate[required]" placeholder="Shipping Port">
                                </div>
                            </div>
                            {!! htmlspecialchars_decode(Form::label('shipping_term', 'Shipping Term <span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <select name="shipping_term{{$cnt}}" id="shipping_term{{$cnt}}" class="form-control validate[required]">
                                        <option value="FOB">FOB</option>
                                        <option value="EXW">EXW</option>
                                    </select>
                                </div>
                            </div>
                            {!! htmlspecialchars_decode(Form::label('weight', 'Weight (Kgs)<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="weight{{$cnt}}" id="weight{{$cnt}}" class="form-control validate[required]" placeholder="Weight">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('chargable_weight', 'chargable Weight (Kgs)<span class="required"></span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="chargable_weight{{$cnt}}" id="chargable_weight{{$cnt}}" class="form-control " placeholder="chargable Weight">
                                </div>
                            </div>
                            {!! htmlspecialchars_decode(Form::label('cubic_meter', 'Cubic Meter<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="cubic_meter{{$cnt}}" id="cubic_meter{{$cnt}}" class="form-control validate[required]" placeholder="Cubic Meter">
                                </div>
                            </div>
                            {!! htmlspecialchars_decode(Form::label('pallet', 'No Of Pallets<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="pallet{{$cnt}}" id="pallet{{$cnt}}" class="form-control validate[required]" placeholder="# Of Pallets">
                                </div>
                            </div>
                        </div>
                            <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('charges', 'Charges<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    @foreach($charges as $charge)
                                    <input type="checkbox" class="validate[groupRequired[charge], minCheckbox[1]]" name="charges{{$cnt}}[]" id="charges{{$cnt}}_{{$charge->id}}" value="{{$charge->id}}" onchange="get_total({{$cnt}},{{$charge->id}},{{$charge->price}})">{{$charge->name}}<br>
                                    @endforeach
                                        <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ $charge->id }}">
                                </div>
                            </div>

                            {!! htmlspecialchars_decode(Form::label('total_shipping_cost', 'Total Shipping Cost<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="total_shipping_cost{{$cnt}}" id="total_shipping_cost{{$cnt}}" class="form-control validate[required]" value="0" readonly>
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

        function  get_total(no,sub_no,price) {

            total=0;
            if($("#charges"+no+"_"+sub_no).is(':checked')) {

                total = parseFloat($("#total_shipping_cost"+no).val(),2) + parseFloat(price,2);
                $("#total_shipping_cost"+no).val(total.toFixed(2));
                $("#total_shipping_cost_span"+no).text(total.toFixed(2));
            }
            else
            {
                total = parseFloat($("#total_shipping_cost"+no).val(),2) - parseFloat(price,2);
                $("#total_shipping_cost"+no).val(total.toFixed(2));
                $("#total_shipping_cost_span"+no).text(total.toFixed(2));
            }
        }

    </script>
@endsection
