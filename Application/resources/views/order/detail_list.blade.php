@extends('layouts.frontend.app')
@section('title', 'Order Management')
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;
            <div class="col-md-10">
                <h2 class="page-head-line">{{$title}}</h2>
            </div>

            <div class="col-md-2 ">
                @if(isset($user_role))
                    @if($id==0)
                         <a href="{{ url('order/index') }}" class="btn btn-primary">Order Management</a>
                    @else
                        <a href="{{ url('order/orderhistory') }}" class="btn btn-primary">Order History</a>
                    @endif
                    @else
                <a href="{{ url('order/ordershipping') }}" class="btn btn-primary">Order Shipping</a>
                @endif
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('shipment_info', 'Shipment Info', ['class' => 'control-label']) !!}
            </div>
        </div>
        {{--*/ $cnt = 0 /*--}}
        {{--*/ $shipment_id = 0 /*--}}
        {{--*/ $cntData = 1 /*--}}
         @foreach($shipment_detail as $shipment_details)
                @if($shipment_id != $shipment_details['shipment_id'])
                    {{--*/ $cnt++ /*--}}
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('shipment_'.$shipment_details['shipment_id'], 'Shipment '.$cnt, ['class' => 'control-label col-md-6']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('method_'.$shipment_details['shipment_id'], $shipment_details['shipping_name'] , ['class' => 'control-label col-md-6']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-1">&nbsp;</div>
                            {!! Form::label('product_desc1_1', 'Product Description', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('suppliers', 'Suppliers', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('inspection', 'Inspections', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('upc_fnsku1_1', 'UPC/FNSKU', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('qty_per_case1_1', 'Qty Per Case', ['class' => 'control-label col-md-1']) !!}
                            {!! Form::label('no_of_case1_1', '# Of Case', ['class' => 'control-label col-md-1']) !!}
                            {!! Form::label('total1_1', 'Total', ['class' => 'control-label col-md-1']) !!}
                        </div>
                    </div>
                @endif
            <div class="col-md-12">
                <div class="form-group">
                    <a id="detail_show_{{$cntData}}" class="col-md-1" href="javascript:void(0)" onclick="detailInfo({{$cntData}})">
                        <span class="glyphicon glyphicon-plus"></span>
                    </a>
                    <div class="col-md-2">
                        <div class="input-group">
                            @if($shipment_details['product_nick_name']==''){{ $shipment_details['product_name']}} @else {{$shipment_details['product_nick_name']}} @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            {{$shipment_details['company_name']}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            {{$shipment_details['inspection_decription']}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            {{$shipment_details['fnsku']}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <div class="input-group">
                            {{$shipment_details['qty_per_box']}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <div class="input-group">
                            {{$shipment_details['no_boxs']}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <div class="input-group">
                            {{$shipment_details['total']}}
                        </div>
                    </div>
                </div>
            </div>

            @if($shipment_id != $shipment_details['shipment_id'])
                {{--*/ $shipment_id = $shipment_details['shipment_id'] /*--}}
            @endif
            <div class="col-md-12" >&nbsp;</div>
            <div id="child_view_{{$cntData}}" style="display:none">
                <div class="col-md-12" >
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('label', 'Label:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['label_name']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('prep_service', 'Prep Service Name:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['prep_service_name']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('prep_service_total', 'Prep Service Total:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['prep_service_total']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('listing_service', 'Listing Service Name:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['listing_service_name']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('listing_service_total', 'Listing Service Total:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['listing_service_total']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('destination_name', 'Destination Name:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['destination_name']}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            {!! Form::label('outbound_name', 'Outbound Method:', ['class' => 'control-label col-md-2']) !!}
                            <div class="input-group col-md-10">
                                {{$shipment_details['outbound_name']}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" >&nbsp;</div>
            {{--*/ $cntData++ /*--}}
         @endforeach
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('payment_info', 'Payment Info', ['class' => 'control-label']) !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-6">
                    {!! Form::label('card_type', 'Card Type:', ['class' => 'control-label col-md-3']) !!}
                    <div class="input-group col-md-3">
                        {{$payment_detail['credit_card_type']}}
                    </div>
                </div>
                <div class="col-md-6">
                    {!! Form::label('card_number', 'Card Number:', ['class' => 'control-label col-md-3']) !!}
                    <div class="input-group col-md-3">
                        {{$payment_detail['credit_card_number']}}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                {!! Form::label('total_cost', 'Total Cost:', ['class' => 'control-label col-md-2']) !!}
                <div class="input-group">
                    {{$payment_detail['total_cost']}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function detailInfo(id){
           $("#child_view_"+id).toggle();
        }
    </script>
@endsection