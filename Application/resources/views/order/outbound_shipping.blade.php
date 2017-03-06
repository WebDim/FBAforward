@extends('layouts.frontend.app')

@section('title', 'Outbound Shipping Information')

@section('content')
@include('layouts.frontend.tabs', ['data' => 'outbound_shipping'])
<div class="row">
    <div class="col-md-12">&nbsp;</div>
</div>
<div class="row">
    <div class="col-md-12">
        {!! Form::open(['url' =>  'order/outbondshipping', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
       {{--*/$ship_count=1/*--}}
        @foreach($shipment as $shipments)
            <div>
                <h4>Shipment # {{$ship_count}} ({{ $shipments->shipping_name }} FREIGHT)</h4>
                <input type="hidden" name="order_id" id="order_id" value="{{ $shipments->order_id }}">

                <div class="table-responsive no-padding">
                    <table class="table" id="list">
                        <thead>
                        <tr>
                            <th class="col-md-5"><span>Product</span></th>
                            <th class="col-md-2"><span>Qty</span></th>
                            <th class="col-md-2"><span>Outbound Method</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--*/$count=1/*--}}
                            @foreach($product as $products)
                                @if($products->shipment_id==$shipments->shipment_id)
                                    <input type="hidden" name="outbound_shipping_detail_id{{$ship_count."_".$count}}" id="outbound_shipping_detail_id{{$ship_count."_".$count}}" value="{{$products->outbound_shipping_detail_id}}">
                                    <input type="hidden" name="shipment_detail_id{{$ship_count."_".$count}}" id="shipment_detail_id{{$ship_count."_".$count}}" value="{{ $products->shipment_detail_id }}">
                            <tr>
                                <td><input type="hidden" name="product_id{{$ship_count."_".$count}}" id="product_id{{$ship_count."_".$count}}" value="{{ $products->product_id }}"> @if($products->product_nick_name==''){{ $products->product_name}} @else {{$products->product_nick_name}} @endif</td>
                                <td><input type="hidden" name="total_unit{{$ship_count."_".$count}}" id="total_unit{{$ship_count."_".$count}}" value="{{ $products->total }}">{{ $products->total }}</td>
                                <td>
                                    <select name="outbound_method{{$ship_count."_".$count}}" class="form-control select2 validate[required]">
                                        <option value="">Select Outbound Methods</option>
                                        @foreach ($outbound_method as $outbound_methods)
                                            <option value="{{ $outbound_methods->outbound_method_id }}" @if($products->outbound_method_id==$outbound_methods->outbound_method_id){{ "selected" }} @endif>  {{ $outbound_methods->outbound_name }}</option>
                                        @endforeach
                                    </select>

                                </td>
                            </tr>
                            {{--*/$count++/*--}}
                            @endif
                            @endforeach
                            <input type="hidden" id="count{{$ship_count}}" name="count{{$ship_count}}" value="{{$count}}">
                        </tbody>
                    </table>
                </div>
            </div>
            {{--*/$ship_count++/*--}}
                    @endforeach
            <input type="hidden" id="ship_count" name="ship_count" value="{{$ship_count}}">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-9 col-md-offset-9">
                    <a href="{{ URL::route('listservice') }}" class="btn btn-primary">Previous</a>
                    {!! Form::submit('  Next  ', ['class'=>'btn btn-primary']) !!}
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