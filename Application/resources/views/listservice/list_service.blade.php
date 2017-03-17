@extends('layouts.frontend.app')

@section('title', 'Listing Services Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'list_service'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'listservice/update', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
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
                    {{--*/ $grand_total = 0 /*--}}
                    @foreach($product as $products)
                        {{--*/ $total = 0 /*--}}
                        {{--*/ $list_service_ids=explode(',', $products->listing_service_ids) /*--}}
                        <tr>
                            <td class="col-md-7">
                                <input type="hidden" name="listing_service_detail_id{{ $cnt }}" value="{{ $products->listing_service_detail_id  }}">
                                <input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <input type="hidden" name="photo_list_detail_id{{$cnt}}" id="photo_list_detail_id{{$cnt}}" value="{{$products->photo_list_detail_id}}">
                                <b class="text-info">@if($products->product_nick_name==''){{ $products->product_name}} @else {{$products->product_nick_name}} @endif</b></td>
                            <td class="col-md-3"><b class="text-info">
                                    {{--*/$listing_standard=env('LIST_STANDARD')/*--}}
                                    {{--*/$listing_standard_price=env('LIST_STANDARD_PRICE')/*--}}
                                    {{--*/$listing_prop=env('LIST_PROP')/*--}}
                                    {{--*/$listing_prop_price=env('LIST_PROP_PRICE')/*--}}
                                    <input type="checkbox" name="service{{$cnt}}_1" id="service{{$cnt}}_1" value="1" onchange="get_total({{$listing_standard_price}},{{$cnt}},1)" @if(in_array(1,$list_service_ids)) {{ "checked" }} @endif>{{ $listing_standard }}<br>
                                    @if(isset($products->photo_list_detail_id) && $products->standard_photo>0)
                                        <div id="standard_div{{$cnt}}">
                                            STANDARD PHOTOS<input type="hidden" id="old_standard{{$cnt}}"  name="old_standard{{$cnt}}"  size="3"   value="{{$products->standard_photo}}"><input type="text" id="standard{{$cnt}}"  name="standard{{$cnt}}"  size="3" class="validate[required]" onchange="get_standard_subtotal(this.value,{{$cnt}},1,{{$listing_standard_price}})" value="{{$products->standard_photo}}"><br>
                                        </div>
                                    @else
                                        <div id="standard_div{{$cnt}}" hidden>
                                            STANDARD PHOTOS<input type="hidden" id="old_standard{{$cnt}}"  name="old_standard{{$cnt}}"  size="3"><input type="text" id="standard{{$cnt}}" name="standard{{$cnt}}"  size="3" class="validate[required]" onchange="get_standard_subtotal(this.value,{{$cnt}},1,{{$listing_standard_price}})"><br>
                                        </div>
                                    @endif
                                    <input type="checkbox" name="service{{$cnt}}_2" id="service{{$cnt}}_2" value="2" onchange="get_total({{$listing_prop_price}},{{$cnt}},2)" @if(in_array(2,$list_service_ids)) {{ "checked" }} @endif>{{ $listing_prop }}<br>
                                    @if(isset($products->photo_list_detail_id) && $products->prop_photo>0)
                                        <div id="prop_div{{$cnt}}">
                                            PROP PHOTOS<input type="hidden" id="old_prop{{$cnt}}" name="old_prop{{$cnt}}" size="3"  value="{{$products->prop_photo}}"><input type="text" id="prop{{$cnt}}" name="prop{{$cnt}}" size="3" class="validate[required]" onchange="get_prop_subtotal(this.value,{{$cnt}},2,{{$listing_prop_price}})" value="{{$products->prop_photo}}"><br>
                                        </div>
                                    @else
                                        <div id="prop_div{{$cnt}}" hidden>
                                            PROP PHOTOS<input type="hidden" id="old_prop{{$cnt}}" name="old_prop{{$cnt}}"  size="3"><input type="text" id="prop{{$cnt}}" name="prop{{$cnt}}" size="3" class="validate[required]" onchange="get_prop_subtotal(this.value,{{$cnt}},2,{{$listing_prop_price}})">
                                        </div>
                                    @endif

                                    @foreach ($list_service as $list_services)
                                        <input type="checkbox" name="service{{$cnt}}_{{ $list_services->listing_service_id }}" id="service{{$cnt}}_{{$list_services->listing_service_id}}" value="{{ $list_services->listing_service_id }}" onchange="get_total({{$list_services->price}},{{$cnt}},{{$list_services->listing_service_id}})" @if(in_array($list_services->listing_service_id,$list_service_ids)) {{ "checked" }} @endif> {{ $list_services->service_name }}<br>
                                    @endforeach
                                        <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{ $list_services->listing_service_id }}">
                                </b></td>
                            <td class="col-md-2"><input type="hidden" id="total{{$cnt}}" name="total{{ $cnt }}" value="{{ isset($products->listing_service_total)? $products->listing_service_total : 0 }}" readonly><b class="text-info"><span id="total_span{{$cnt}}">{{ isset($products->listing_service_total)? $products->listing_service_total : 0 }}</span></b></td>
                        </tr>
                        {{--*/$total= isset($products->listing_service_total)? $products->listing_service_total : 0 /*--}}
                        {{--*/$grand_total=$grand_total+$total/*--}}
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td><strong>Total</strong></td>
                        <td><input type="hidden" id="order_id" name="order_id" value="{{ $products->order_id}}"><input type="hidden" id="grand_total" name="grand_total" value="{{ isset($products->grand_total) ? $products->grand_total : $grand_total}}" readonly><span id="grand_total_span">{{ isset($products->grand_total) ? $products->grand_total : $grand_total}}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="count" value=" {{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        <a href="{{ '/prepservice' }}" class="btn btn-primary">Previous</a>
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
        function get_standard_subtotal(qty,no,service_id,price)
        {

            if($("#old_standard"+no).val()!=$("#standard"+no).val())
            {
                 if($("#old_standard"+no).val()>0) {
                     temp_total = parseFloat($("#old_standard" + no).val(), 2) * parseFloat(price, 2);
                     total = parseFloat($("#total" + no).val(), 2) - parseFloat(temp_total, 2);
                     $("#total" + no).val(total.toFixed(2));
                     $("#total_span" + no).text(total.toFixed(2));
                     grand_total = parseFloat($("#grand_total").val(), 2) - parseFloat(temp_total, 2);
                     $("#grand_total").val(grand_total.toFixed(2));
                     $("#grand_total_span").text(grand_total.toFixed(2));
                     if($("#standard"+no).val()>0) {
                         temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                         total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                         $("#total" + no).val(total.toFixed(2));
                         $("#total_span" + no).text(total.toFixed(2));
                         grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                         $("#grand_total").val(grand_total.toFixed(2));
                         $("#grand_total_span").text(grand_total.toFixed(2));
                     }
                     $("#old_standard"+no).val(qty);
                 }
                 else
                 {
                     if($("#standard"+no).val()>0) {
                         temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                         total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                         $("#total" + no).val(total.toFixed(2));
                         $("#total_span" + no).text(total.toFixed(2));
                         grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                         $("#grand_total").val(grand_total.toFixed(2));
                         $("#grand_total_span").text(grand_total.toFixed(2));
                     }
                     $("#old_standard"+no).val(qty);
                 }
            }
            else {
                temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                $("#total" + no).val(total.toFixed(2));
                $("#total_span" + no).text(total.toFixed(2));
                grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
                $("#old_standard"+no).val(qty);
            }


        }
        function get_prop_subtotal(qty,no,service_id,price)
        {

            if($("#old_prop"+no).val()!=$("#prop"+no).val())
            {
                if($("#old_prop"+no).val()>0) {
                    temp_total = parseFloat($("#old_prop" + no).val(), 2) * parseFloat(price, 2);
                    total = parseFloat($("#total" + no).val(), 2) - parseFloat(temp_total, 2);
                    $("#total" + no).val(total.toFixed(2));
                    $("#total_span" + no).text(total.toFixed(2));
                    grand_total = parseFloat($("#grand_total").val(), 2) - parseFloat(temp_total, 2);
                    $("#grand_total").val(grand_total.toFixed(2));
                    $("#grand_total_span").text(grand_total.toFixed(2));
                    if($("#prop"+no).val()>0) {
                        temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                        total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                        $("#total" + no).val(total.toFixed(2));
                        $("#total_span" + no).text(total.toFixed(2));
                        grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                        $("#grand_total").val(grand_total.toFixed(2));
                        $("#grand_total_span").text(grand_total.toFixed(2));
                    }
                    $("#old_prop"+no).val(qty);
                }
                else
                {
                    if($("#prop"+no).val()>0) {
                        temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                        total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                        $("#total" + no).val(total.toFixed(2));
                        $("#total_span" + no).text(total.toFixed(2));
                        grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                        $("#grand_total").val(grand_total.toFixed(2));
                        $("#grand_total_span").text(grand_total.toFixed(2));
                    }
                    $("#old_prop"+no).val(qty);
                }
            }
            else {
                temp_total = parseFloat(qty, 2) * parseFloat(price, 2);
                total = parseFloat($("#total" + no).val(), 2) + parseFloat(temp_total, 2);
                $("#total" + no).val(total.toFixed(2));
                $("#total_span" + no).text(total.toFixed(2));
                grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(temp_total, 2);
                $("#grand_total").val(grand_total.toFixed(2));
                $("#grand_total_span").text(grand_total.toFixed(2));
                $("#old_prop"+no).val(qty);
            }
        }
        function  get_total(price,no,sub_no) {
            if($("#service"+no+"_"+sub_no).is(':checked')) {
                if($('#service'+no+'_'+sub_no).val()==1)
                {
                    $("#standard_div"+no).show();
                }
                else if($('#service'+no+'_'+sub_no).val()==2)
                {
                    $("#prop_div"+no).show();
                }
                else {
                    total = parseFloat($("#total" + no).val(), 2) + parseFloat(price, 2);
                    $("#total" + no).val(total.toFixed(2));
                    $("#total_span" + no).text(total.toFixed(2));
                    grand_total = parseFloat($("#grand_total").val(), 2) + parseFloat(price, 2);
                    $("#grand_total").val(grand_total.toFixed(2));
                    $("#grand_total_span").text(grand_total.toFixed(2));
                }
            }
            else {
                if($('#service'+no+'_'+sub_no).val()==1)
                {
                    photo_list_detail_id = $("#photo_list_detail_id" + no).val();
                    $("#standard_div" + no).hide();
                    temp_total = parseFloat($("#old_standard" + no).val(), 2) * parseFloat(price, 2);
                    total = parseFloat($("#total" + no).val(), 2) - parseFloat(temp_total, 2);
                    $("#total" + no).val(total.toFixed(2));
                    $("#total_span" + no).text(total.toFixed(2));
                    grand_total = parseFloat($("#grand_total").val(), 2) - parseFloat(temp_total, 2);
                    $("#grand_total").val(grand_total.toFixed(2));
                    $("#grand_total_span").text(grand_total.toFixed(2));
                    $("#old_standard" + no).val('');
                    $("#standard" + no).val('');
                    $('.preloader').css("display", "block");
                    $.ajax({
                        headers: {
                            'X-CSRF-Token': $('input[name="_token"]').val()
                        },
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/listservice/removephotolabel', // This is the url we gave in the route
                        data: {
                            'photo_list_detail_id': photo_list_detail_id,
                            'service_id': sub_no,
                        }, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            $('.preloader').css("display", "none");
                            console.log(response);
                            //swal("product deleted Successfully");

                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('.preloader').css("display", "none");
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                }
                else if($('#service'+no+'_'+sub_no).val()==2)
                {
                    photo_list_detail_id = $("#photo_list_detail_id" + no).val();
                    $("#prop_div" + no).hide();
                    temp_total = parseFloat($("#old_prop" + no).val(), 2) * parseFloat(price, 2);
                    total = parseFloat($("#total" + no).val(), 2) - parseFloat(temp_total, 2);
                    $("#total" + no).val(total.toFixed(2));
                    $("#total_span" + no).text(total.toFixed(2));
                    grand_total = parseFloat($("#grand_total").val(), 2) - parseFloat(temp_total, 2);
                    $("#grand_total").val(grand_total.toFixed(2));
                    $("#grand_total_span").text(grand_total.toFixed(2));
                    $("#old_prop" + no).val('');
                    $("#prop" + no).val('');
                    $('.preloader').css("display", "block");
                    $.ajax({
                        headers: {
                            'X-CSRF-Token': $('input[name="_token"]').val()
                        },
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/listservice/removephotolabel', // This is the url we gave in the route
                        data: {
                            'photo_list_detail_id': photo_list_detail_id,
                            'service_id' : sub_no,
                        }, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            $('.preloader').css("display", "none");
                            console.log(response);
                            //swal("product deleted Successfully");

                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('.preloader').css("display", "none");
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                }
                else {
                    total = parseFloat($("#total" + no).val(), 2) - parseFloat(price, 2);
                    $("#total" + no).val(total.toFixed(2));
                    $("#total_span" + no).text(total.toFixed(2));
                    grand_total = parseFloat($("#grand_total").val(), 2) - parseFloat(price, 2);
                    $("#grand_total").val(grand_total.toFixed(2));
                    $("#grand_total_span").text(grand_total.toFixed(2));
                }
            }
        }
    </script>
@endsection