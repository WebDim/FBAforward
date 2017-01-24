@extends('layouts.frontend.app')

@section('title', 'Prep Services Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'prep_service'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/prepservice', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th class="col-md-6"><span>Product</span></th>
                        <th class="col-md-2"><span>Quantity</span></th>
                        <th class="col-md-2"><span>Prep Services</span></th>
                        <th class="col-md-2"><span>Total</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    {{--*/ $grand_total = 0 /*--}}
                    @foreach($product as $products)
                        <input type="hidden" name="prep_detail_id{{ $cnt }}" value="{{ $products->prep_detail_id }}">
                        <input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                        <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                        <input type="hidden" name="qty{{ $cnt }}" value="{{ $products->total }}">
                       {{--*/ $prep_service_ids=explode(',', $products->prep_service_ids) /*--}}
                        <tr>
                            <td class="col-md-6"><b class="text-info">{{ $products->product_name }}</b></td>
                            <td class="col-md-2"><b class="text-info">{{ $products->total }}</b></td>
                            <td class="col-md-2"><b class="text-info">
                            @foreach ($prep_service as $prep_services)
                               <input type="checkbox" name="service{{$cnt}}_{{$prep_services->prep_service_id}}" id="service{{$cnt}}_{{$prep_services->prep_service_id}}" value="{{  $prep_services->prep_service_id }}" onchange="get_total({{$prep_services->price}},{{$cnt}},{{$prep_services->prep_service_id}})" @if(in_array($prep_services->prep_service_id,$prep_service_ids)) {{ "checked" }} @endif>{{ $prep_services->service_name }}
                               <br>
                            @endforeach
                            <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ $prep_services->prep_service_id }}">
                            </b></td>
                            <td class="col-md-2"><input type="hidden" id="total{{$cnt}}" name="total{{ $cnt }}" value="{{ isset($products->prep_service_total)? $products->prep_service_total : 0 }}" readonly><b class="text-info"><span id="total_span{{$cnt}}">{{ isset($products->prep_service_total)? $products->prep_service_total : 0 }}</span></b></td>
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td><input type="hidden" id="order_id" name="order_id" value="{{ $products->order_id}}"><input type="hidden" id="grand_total" name="grand_total" value="{{ isset($products->grand_total) ? $products->grand_total : 0}}"><span id="grand_total_span">{{ isset($products->grand_total) ? $products->grand_total : 0}}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="count" value=" {{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        <a href="{{ URL::route('productlabels') }}" class="btn btn-primary">Previous</a>
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

        function  get_total(price,no,sub_no) {
            if($("#service"+no+"_"+sub_no).is(':checked')) {
                total = parseFloat($("#total"+no).val(),2) + parseFloat(price,2);
                $("#total"+no).val(total.toFixed(2));
                $("#total_span"+no).text(total.toFixed(2));
                grand_total=parseFloat($("#grand_total").val(),2)+parseFloat(price,2);
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
            }
            else
            {
                total = parseFloat($("#total" + no).val(),2) - parseFloat(price,2);
                $("#total"+no).val(total.toFixed(2));
                $("#total_span"+no).text(total.toFixed(2));
                grand_total=parseFloat($("#grand_total").val(),2)-parseFloat(price,2);
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
            }
        }
    </script>
@endsection