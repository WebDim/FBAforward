@extends('layouts.frontend.app')

@section('title', 'Listing Services Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'list_service'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/listservice', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th class="col-md-7"><span>Product</span></th>
                        <th class="col-md-3"><span>Listing Services</span></th>
                        <th class="col-md-2"><span>Total</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    @foreach($product as $products)

                        {{--*/ $list_service_ids=explode(',', $products->listing_service_ids) /*--}}
                        <tr>
                            <td class="col-md-7">
                                <input type="hidden" name="listing_service_detail_id{{ $cnt }}" value="{{ $products->listing_service_detail_id  }}">
                                <input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <b class="text-info">{{ $products->product_name }}</b></td>
                            <td class="col-md-3"><b class="text-info">
                                    @foreach ($list_service as $list_services)
                                        <input type="checkbox" name="service{{$cnt}}_{{ $list_services->listing_service_id }}" id="service{{$cnt}}_{{$list_services->listing_service_id}}" value="{{ $list_services->listing_service_id }}" onchange="get_total({{$list_services->price}},{{$cnt}},{{$list_services->listing_service_id}})" @if(in_array($list_services->listing_service_id,$list_service_ids)) {{ "checked" }} @endif>{{ $list_services->service_name }}<br>
                                    @endforeach
                                        <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ $list_services->listing_service_id }}">
                                </b></td>
                            <td class="col-md-2"><input type="hidden" id="total{{$cnt}}" name="total{{ $cnt }}" value="{{ isset($products->listing_service_total)? $products->listing_service_total : 0 }}" readonly><b class="text-info"><span id="total_span{{$cnt}}">{{ isset($products->listing_service_total)? $products->listing_service_total : 0 }}</span></b></td>
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td><input type="hidden" id="order_id" name="order_id" value="{{ $products->order_id}}"><input type="hidden" id="grand_total" name="grand_total" value="{{ isset($products->grand_total) ? $products->grand_total : 0}}" readonly><span id="grand_total_span">{{ isset($products->grand_total) ? $products->grand_total : 0}}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="count" value=" {{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        <a href="{{ URL::route('prepservice') }}" class="btn btn-primary">Previous</a>
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