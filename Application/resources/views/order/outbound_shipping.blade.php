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
        @foreach($shipment as $key=>$shipments)
        <div>
            Shipment #{{ $key+1 }} ({{$shipments->shipping_name}} FREIGHT)
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
                    {{--*/$cnt=1 /*--}}
                    @foreach($amazon_destination as $amazon_destinatios)

                    <tr>
                        <td class="col-md-3"><b class="text-info">{{$amazon_destinatios->destination_name }}</b></td>
                     <td class="col-md-5"><b class="text-info">
                        @foreach($product as $products)
                            @if($products->shipment_id==$shipments->shipment_id)
                                {{ $products->product_name }}  <br>
                            @endif
                        @endforeach
                         </b>
                     </td>
                     <td class="col-md-2">
                         <b class="text-info">
                         @foreach($product as $products)
                             @if($products->shipment_id==$shipments->shipment_id)
                                 {{ $products->total }}  <br>
                             @endif
                         @endforeach
                         </b>
                     </td>
                     <td class="col-md-2">
                         <select name="outbound_method{{ $cnt }}" class="form-control select2 validate[required]">
                             <option value="">Select Outbound Methods</option>
                             @foreach ($outbound_method as $outbound_methods)
                                 <option value="{{ $outbound_methods->outbound_method_id }}">  {{ $outbound_methods->outbound_name }}</option>
                             @endforeach
                         </select>
                     </td>
                    </tr>
                        {{--*/$cnt++/*--}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
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