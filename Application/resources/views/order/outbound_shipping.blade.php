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
        {{--*/ $ship_count=1 /*--}}
        @foreach($detail as $key=>$details)
            <input type="hidden" name="shipment_id{{$ship_count}}" value="{{ $details['shipment_id'] }}">
        <div>
            <h4>Shipment # {{$ship_count}}({{$details['shipment_name']}} FREIGHT)</h4>
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
                    <input type="hidden" name="order" id="order" value="{{ $details['order'] }}">
                    {{--*/ $cnt=1 /*--}}
                   @foreach($details['destination'] as $destination=>$product)
                       <tr>
                           <td><b class="text-info"> {{ $destination }}</b></td>
                           <td hidden>
                               {{--*/$product_cnt=1/*--}}
                               @foreach($product as $products)
                                   <input type="hidden" name="product_id{{$ship_count."_".$cnt."_".$product_cnt}}" value="{{$products['product_id']}}">
                                   <input type="hidden" name="total_unit{{$ship_count."_".$cnt."_".$product_cnt}}" value="{{$products['qty']}}">
                                   <input type="hidden" name="amazon_destination_id{{$ship_count."_".$cnt."_".$product_cnt}}" value="{{$products['destination_id']}}">
                                   {{--*/$product_cnt++/*--}}
                               @endforeach
                           </td>
                           <td><b class="text-info">@foreach($product as $products){{$products['product_name']}}<br>@endforeach</b></td>
                           <td><b class="text-info">@foreach($product as $products){{$products['qty']}}<br>@endforeach</b></td>
                           <td>
                               <select name="outbound_method{{$ship_count."_".$cnt}}" class="form-control select2 validate[required]">
                                   <option value="">Select Outbound Methods</option>
                                   @foreach ($outbound_method as $outbound_methods)
                                       <option value="{{ $outbound_methods->outbound_method_id }}">  {{ $outbound_methods->outbound_name }}</option>
                                   @endforeach
                               </select>
                           </td>
                       </tr>
                       <input type="hidden" name="product_count{{$cnt}}" id="product_count{{$cnt}}" value="{{$product_cnt}}">
                       {{--*/$cnt++ /*--}}
                   @endforeach
                    <input  type="hidden" name="count{{$ship_count}}" id="count{{$ship_count}}" value="{{ $cnt }}">
                    </tbody>
                </table>
            </div>
        </div>

            {{--*/$ship_count++ /*--}}
        @endforeach
        <input type="hidden" name="ship_count" id="ship_count" value="{{ $ship_count }}">
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