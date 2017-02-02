@extends('layouts.frontend.app')
@section('title', 'Order Management')
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;
            <div class="col-md-10">
                <h2 class="page-head-line">ORDER DETAIL</h2>
            </div>
            <div class="col-md-2 ">
                <a href="{{ url('order/index') }}" class="btn btn-primary">Order Management</a>
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
                            {!! Form::label('product_desc1_1', 'Product Description', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('suppliers', 'Suppliers', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('inspection', 'Inspections', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('label', 'Labels', ['class' => 'control-label col-md-1']) !!}
                            {!! Form::label('upc_fnsku1_1', 'UPC/FNSKU', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('qty_per_case1_1', 'Qty Per Case', ['class' => 'control-label col-md-1']) !!}
                            {!! Form::label('no_of_case1_1', '# Of Case', ['class' => 'control-label col-md-1']) !!}
                            {!! Form::label('total1_1', 'Total', ['class' => 'control-label col-md-1']) !!}
                        </div>
                    </div>
                @endif
            <div class="col-md-12">&nbsp;
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            {{$shipment_details['product_name']}}
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
                    <div class="col-md-1">
                        <div class="input-group">
                            {{$shipment_details['label_name']}}
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
         @endforeach
    </div>
@endsection