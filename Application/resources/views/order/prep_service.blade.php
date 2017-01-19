@extends('layouts.frontend.app')

@section('title', 'Prep Services Information')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Prep Services Information</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/prepservice', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <td><span>Product</span></td>
                        <td><span>Quantity</span></td>
                        <td><span>Prep Services</span></td>
                        <td><span>Total</span></td>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    {{--*/ $grand_total = 0 /*--}}
                    @foreach($product as $products)

                        <tr>
                            <td><input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <b class="text-info">{{ $products->product_name }}</b></td>
                            <td><input type="hidden" name="qty{{ $cnt }}" value="{{ $products->total }}">
                                <b class="text-info">{{ $products->total }}</b></td>
                            <td><b class="text-info">
                                    {{--*/ $total=0 /*--}}

                                    @foreach ($prep_service as $prep_services)
                                        <input type="checkbox" name="service{{$cnt}}_{{$prep_services->prep_service_id}}" id="service{{$cnt}}_{{$prep_services->prep_service_id}}" value="{{  $prep_services->prep_service_id }}" onchange="get_total({{$prep_services->price}},{{$cnt}},{{$prep_services->prep_service_id}})">{{ $prep_services->service_name }}
                                        {{--*/ $total = $total+$prep_services->price /*--}}

                                        <br>
                                    @endforeach
                                    <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ count($prep_service) }}">

                            </b></td>
                            <td><b class="text-info"><input type="text" id="total{{$cnt}}" name="total{{ $cnt }}" value="0" readonly></b></td>
                            {{--*/ $grand_total =$grand_total+$total /*--}}
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td><input type="text" id="grand_total" name="grand_total" value="0" readonly></td>
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
                total = parseInt($("#total"+no).val()) + parseInt(price);
                $("#total"+no).val(total);
                grand_total=parseInt($("#grand_total").val())+parseInt(price);
                $("#grand_total").val(grand_total);
            }
            else
            {
                total = parseInt($("#total" + no).val() ) - parseInt(price);
                $("#total"+no).val(total);
                grand_total=parseInt($("#grand_total").val())-parseInt(price);
                $("#grand_total").val(grand_total);
            }
        }
    </script>
@endsection