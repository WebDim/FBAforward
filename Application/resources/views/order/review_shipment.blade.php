@extends('layouts.frontend.app')

@section('title', 'Review Shipment Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'review_shipment'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6">
                    <h4>Shipments</h4>
                    <div class="table-responsive no-padding">
                        <table class="table" id="list">
                            <thead>
                            <tr>
                                <th class="col-md-3"><b class="text-info">Shipment</b></th>
                                <th class="col-md-5"><b class="text-info">Total Units</b></th>
                                <th class="col-md-2"><b class="text-info">Methods</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shipment as $key=>$shipments)
                                <tr>
                                    <td><b class="text-info">Shipment #{{$key+1}}</b></td>
                                    <td><b class="text-info">{{ $shipments->total }}</b></td>
                                    <td><b class="text-info">{{ $shipments->shipping_name }}</b></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4>Outbound</h4>
                    <div class="table-responsive no-padding">
                        <table class="table" id="list">
                            <thead>
                            <tr>
                                <th class="col-md-5"><b class="text-info">Product</b></th>
                                <th class="col-md-2"><b class="text-info">Units</b></th>
                                <th class="col-md-2"><b class="text-info">Outbound Method</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($outbound_detail as $outbound_details)
                                <tr>
                                    <td class="col-md-5"><b class="text-info">@if($outbound_details->product_nick_name==''){{ $outbound_details->product_name}} @else {{$outbound_details->product_nick_name}} @endif</b></td>
                                    <td class="col-md-2"><b class="text-info">{{ $outbound_details->qty }}</b></td>
                                    <td class="col-md-2"><b class="text-info">{{ $outbound_details->outbound_name }}</b></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4>Products</h4>
                    <div class="table-responsive no-padding">
                        <table class="table" id="list">
                            <thead>
                            <tr>
                                <th class="col-md-5"><b class="text-info">Product</b></th>
                                <th class="col-md-2"><b class="text-info">Qty</b></th>
                                <th class="col-md-2"><b class="text-info">Services</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product_detail as $product_details)

                                <tr>
                                    <td><b class="text-info">@if($product_details->product_nick_name==''){{ $product_details->product_name}} @else {{$product_details->product_nick_name}} @endif</b></td>
                                    <td><b class="text-info">{{ $product_details->total }}</b></td>
                                    <td><b class="text-info">
                                        {{--*/$prep_ids=array()/*--}}
                                        {{--*/$prep_ids=explode(',',$product_details->prep_service_ids)/*--}}
                                        @foreach($prep_ids as $ids)
                                        @foreach($prep_service as $prep_services)
                                            @if($prep_services->prep_service_id==$ids)
                                                {{ $prep_services->service_name  }}<br>
                                            @endif
                                        @endforeach
                                        @endforeach
                                        </b>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-9 col-md-offset-9">
                    <a href="{{ '/outboundshipping' }}" class="btn btn-primary">Previous</a>
                    <a href="{{ '/payment' }}" class="btn btn-primary">Next</a>

                </div>
            </div>

        </div>
    </div>
@endsection