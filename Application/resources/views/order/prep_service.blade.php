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
                        {{--*/ $total = 0 /*--}}
                        <input type="hidden" name="prep_detail_id{{ $cnt }}" value="{{ $products->prep_detail_id }}">
                        <input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                        <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                        <input type="hidden" name="qty{{ $cnt }}" value="{{ $products->total }}">
                        <input type="hidden" name="other_label_detail_id{{$cnt}}" id="other_label_detail_id{{$cnt}}" value="{{ $products->other_label_detail_id }}">
                        {{--*/ $prep_service_ids=explode(',', $products->prep_service_ids) /*--}}
                        <tr>
                            <td class="col-md-6"><b class="text-info">{{ $products->product_name }}</b></td>
                            <td class="col-md-2"><b class="text-info">{{ $products->total }}</b></td>
                            <td class="col-md-2"><b class="text-info">
                            @foreach ($prep_service as $prep_services)
                               <input type="checkbox" name="service{{$cnt}}_{{$prep_services->prep_service_id}}" id="service{{$cnt}}_{{$prep_services->prep_service_id}}" value="{{  $prep_services->prep_service_id }}" onchange="get_total({{$products->total}},{{$prep_services->price}},{{$cnt}},{{$prep_services->prep_service_id}})" @if(in_array($prep_services->prep_service_id,$prep_service_ids)) {{ "checked" }} @endif>{{ $prep_services->service_name }}
                               <br>
                                                            @endforeach
                            <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ $prep_services->prep_service_id }}">
                                @if(isset($products->other_label_detail_id))
                                <div name="other_label_div{{$cnt}}" id="other_label_div{{$cnt}}" >
                                <select name="other_label{{$cnt}}" id="other_label{{$cnt}}" class="form-control validate[required]">
                                    <option value="">Selecet Label</option>
                                    <option value="1" @if($products->label_id=='1'){{"selected"}} @endif>Suffocation Warning</option>
                                    <option value="2" @if($products->label_id=='2'){{"selected"}} @endif>This is a Set</option>
                                    <option value="3" @if($products->label_id=='3'){{"selected"}} @endif>Blank</option>
                                    <option value="4" @if($products->label_id=='4'){{"selected"}} @endif>Custom</option>
                                </select>
                                </div>
                                @else
                                <div name="other_label_div{{$cnt}}" id="other_label_div{{$cnt}}" hidden>
                                <select name="other_label{{$cnt}}" id="other_label{{$cnt}}" class="form-control validate[required]">
                                    <option value="">Selecet Label</option>
                                    <option value="1" @if($products->label_id=='1'){{"selected"}} @endif>Suffocation Warning</option>
                                    <option value="2" @if($products->label_id=='2'){{"selected"}} @endif>This is a Set</option>
                                    <option value="3" @if($products->label_id=='3'){{"selected"}} @endif>Blank</option>
                                    <option value="4" @if($products->label_id=='4'){{"selected"}} @endif>Custom</option>
                                </select>
                                </div>
                                @endif
                            </b>
                            </td>
                            <td class="col-md-2"><input type="hidden" id="total{{$cnt}}" name="total{{ $cnt }}" value="{{ isset($products->prep_service_total)? $products->prep_service_total : 0 }}" readonly><b class="text-info"><span id="total_span{{$cnt}}">{{ isset($products->prep_service_total)? $products->prep_service_total : 0 }}</span></b></td>
                        </tr>
                        {{--*/$total= isset($products->prep_service_total)? $products->prep_service_total : 0 /*--}}
                        {{--*/$grand_total=$grand_total+$total/*--}}
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td><input type="hidden" id="order_id" name="order_id" value="{{ $products->order_id}}"><input type="hidden" id="grand_total" name="grand_total" value="{{ isset($products->grand_total) ? $products->grand_total : $grand_total}}"><span id="grand_total_span">{{ isset($products->grand_total) ? $products->grand_total : $grand_total}}</span></td>
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

        function  get_total(qty,price,no,sub_no) {

            temp_total=parseFloat(qty,2)*parseFloat(price,2);
            if($("#service"+no+"_"+sub_no).is(':checked')) {

                if($('#service'+no+'_'+sub_no).val()==2)
                {
                    $("#other_label_div"+no).show();
                }

                total = parseFloat($("#total"+no).val(),2) + temp_total;
                $("#total"+no).val(total.toFixed(2));
                $("#total_span"+no).text(total.toFixed(2));
                grand_total=parseFloat($("#grand_total").val(),2)+temp_total;
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
            }
            else
            {
                if($('#service'+no+'_'+sub_no).val()==2) {
                    $("#other_label_div" + no).hide();
                    label_detail_id = $("#other_label_detail_id" + no).val();
                    $.ajax({
                        headers: {
                            'X-CSRF-Token': $('input[name="_token"]').val()
                        },
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/order/removeotherlabel', // This is the url we gave in the route
                        data: {
                            'label_detail_id': label_detail_id,

                        }, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            console.log(response);
                            //alert("product deleted Successfully");

                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                }
                total = parseFloat($("#total" + no).val(),2) - temp_total;
                $("#total"+no).val(total.toFixed(2));
                $("#total_span"+no).text(total.toFixed(2));
                grand_total=parseFloat($("#grand_total").val(),2)-temp_total;
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
            }
        }

    </script>
@endsection