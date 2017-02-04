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
                                <th class="col-md-3"><span>Shipment</span></th>
                                <th class="col-md-5"><span>Total Units</span></th>
                                <th class="col-md-2"><span>Methods</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shipment as $key=>$shipments)
                                <tr>
                                <td>Shipment #{{$key+1}}</td>
                                    <td>{{ $shipments->total }}</td>
                                    <td>{{ $shipments->shipping_name }}</td>
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
                                <th class="col-md-5"><span>Product</span></th>
                                <th class="col-md-2"><span>Units</span></th>
                                <th class="col-md-2"><span>Outbound Method</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($outbound_detail as $outbound_details)
                                <tr>
                                    <td class="col-md-5"><span>{{ $outbound_details->product_name }}</span></td>
                                    <td class="col-md-2"><span>{{ $outbound_details->qty }}</span></td>
                                    <td class="col-md-2"><span>{{ $outbound_details->outbound_name }}</span></td>
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
                                <th class="col-md-5"><span>Product</span></th>
                                <th class="col-md-2"><span>Qty</span></th>
                                <th class="col-md-2"><span>Services</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product_detail as $product_details)

                                <tr>
                                    <td>{{ $product_details->product_name }}</td>
                                    <td>{{ $product_details->total }}</td>
                                    <td>
                                        {{--*/$prep_ids=array()/*--}}
                                        {{--*/$prep_ids=explode(',',$product_details->prep_service_ids)/*--}}
                                        @foreach($prep_ids as $ids)
                                        @foreach($prep_service as $prep_services)
                                            @if($prep_services->prep_service_id==$ids)
                                                {{ $prep_services->service_name  }}<br>
                                            @endif
                                        @endforeach
                                        @endforeach
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
                    <a href="{{ URL::route('outbondshipping') }}" class="btn btn-primary">Previous</a>
                    <a href="{{ URL::route('payment') }}" class="btn btn-primary">Next</a>

                </div>
            </div>

        </div>
    </div>
@endsection