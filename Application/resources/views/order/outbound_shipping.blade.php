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
        {{--*/$ship_cnt=1 /*--}}
        @foreach($shipment as $shipments)
            {{--*/$cnt=1 /*--}}
        <div>
            Shipment #{{ $ship_cnt}} ({{$shipments->shipping_name}} FREIGHT)
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th class="col-md-3"><span>Amazon Destination</span></th>
                        <th class="col-md-5"><span>Product</span></th>
                        <th class="col-md-2"><span>Qty</span></th>
                        <th class="col-md-2"><span>Outbound Method</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <input type="hidden" id="order_id" name="order_id" value="{{$shipments->order_id}}">
                    <input type="hidden" id="shipment_id{{$ship_cnt}}" name="shipment_id{{$ship_cnt}}" value="{{$shipments->shipment_id}}">

                    @foreach($amazon_destination as $amazon_destinations)


                        <tr>
                        <td class="col-md-3"><input type="hidden" name="amazon_destination_id{{ $ship_cnt."_".$cnt }}" value="{{ $amazon_destinations->amazon_destination_id }}"><b class="text-info">{{$amazon_destinations->destination_name }}</b></td>
                        <td class="col-md-5"><b class="text-info">
                                {{--*/$product_cnt=1 /*--}}
                          @foreach($product as $products)
                            @if($products->shipment_id==$shipments->shipment_id)
                                {{ $products->product_name }}  <br>
                                <input type="hidden" name="product_id{{$ship_cnt."_".$cnt."_".$product_cnt}}" value="{{$products->product_id}}">
                                                 {{--*/$product_cnt++/*--}}
                           @endif
                        @endforeach

                         </b>
                            {{--*/ $method= array(); /*--}}
                            @foreach($outbound_detail as $outbound_details)
                                @if($outbound_details->shipment_id==$shipments->shipment_id && $outbound_details->amazon_destination_id==$amazon_destinations->amazon_destination_id)
                                    <input type="hidden" name="outbound_shipping_detail_id{{$ship_cnt."_".$cnt}}" value="{{$outbound_details->outbound_shipping_detail_id}}">
                                {{--*/ $method[]= $outbound_details->outbound_method_id /*--}}
                                @endif
                            @endforeach
                        </td>
                     <td class="col-md-2">
                         {{--*/$product_cnt=1 /*--}}
                         <b class="text-info">
                         @foreach($product as $products)

                             @if($products->shipment_id==$shipments->shipment_id)
                                 {{ $products->total }}  <br>
                                     <input type="hidden" name="total_unit{{$ship_cnt."_".$cnt."_".$product_cnt}}" value="{{$products->total}}">
                             {{--*/$product_cnt++ /*--}}
                                     <input type="hidden" name="product_count{{$ship_cnt."_".$cnt}}"  value="{{$product_cnt}}">
                             @endif
                         @endforeach
                         </b>
                     </td>
                     <td class="col-md-2">

                         <select name="outbound_method{{ $ship_cnt."_".$cnt }}" class="form-control select2 validate[required]">
                             <option value="">Select Outbound Methods</option>
                             @foreach ($outbound_method as $outbound_methods)
                                 <option value="{{ $outbound_methods->outbound_method_id }}" @if(!empty($method))@if($method[0] ==$outbound_methods->outbound_method_id) {{ "selected" }} @endif @endif>  {{ $outbound_methods->outbound_name }}</option>
                             @endforeach
                         </select>
                     </td>

                    </tr>
                        {{--*/$cnt++ /*--}}
                        @endforeach
                    <input type="hidden" name="count{{$ship_cnt}}" value="{{$cnt}}">
                    </tbody>
                </table>
            </div>
        </div>
            {{--*/$ship_cnt++/*--}}
        @endforeach
        <input type="hidden" name="ship_count" id="ship_count" value="{{$ship_cnt}}">
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