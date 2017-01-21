@extends('layouts.frontend.app')
@section('title', 'Shipment call')
@section('content')
    @include('layouts.frontend.tabs', ['data' => 'shipment'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/shipment', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('ship_count', old('ship_count',1), ['class' => 'form-control', 'id'=>'ship_count']) !!}
            {!! Form::hidden('order_id', old('order_id', count($shipment)>0 ? $shipment[0]->order_id  : null), ['class' => 'form-control']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('split_shipment', 'Split Shipment *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <select name="split_shipment" id="split_sphiment" class="form-control select2 validate[required]" onchange="get_Split_shipment(this.value)">
                                    <option value="0" @if(count($shipment)>0) @if($shipment[0]->split_shipment=="0"){{"selected"}} @endif @endif > No</option>
                                    <option value="1" @if(count($shipment)>0) @if($shipment[0]->split_shipment=="1"){{"selected"}} @endif @endif >Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date', 'Goods Ready Date *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('date', old('date', count($shipment)>0 ? date('m/d/Y',strtotime( $shipment[0]->goods_ready_date)) : null), ['class' => 'datepicker form-control validate[required]', 'placeholder'=>'mm/dd/yyyy']) !!}
                            </div>
                        </div>
                    </div>
                </div><!-- .col-md-6 -->

                <div class="col-md-12" id="main1">
                    <hr>
                    <div class="form-group">
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-addon">Shipment 1</span>
                            </div>
                        </div>
                        {!! Form::label('shipping_method1', 'Shipping Method *', ['class' => 'control-label col-md-2']) !!}
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <select name="shipping_method1" class="form-control select2 validate[required]">
                                    <option value=""> Select Shipping Method</option>
                                    @foreach ($shipping_method as $ship_method)
                                        <option value="{{ $ship_method->shipping_method_id }}"  @if(count($shipment)>0) @if($shipment[0]->shipping_method_id==$ship_method->shipping_method_id){{"selected"}} @endif @endif >  {{ $ship_method->shipping_name }}</option>
                                    @endforeach
                                </select>
                                {!! Form::hidden('shipment_id1', old('shipment_id1', count($shipment)>0 ? $shipment[0]->shipment_id  : null), ['class' => 'form-control']) !!}

                            </div>
                        </div>
                    </div>
                    @if(count($shipment)>0)
                        {{--*/ $cnt=1 /*--}}
                        @foreach($shipment_detail as $shipment_details)
                            @if($shipment[0]->shipment_id==$shipment_details->shipment_id)
                                <div class="form-group" id="label1_{{$cnt}}">
                                    {!! Form::label('product_desc1_1', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('upc_fnsku1_1', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('qty_per_case1_1', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('no_of_case1_1', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('total1_1', 'Total *', ['class' => 'control-label col-md-2']) !!}
                                </div>
                                <div class="form-group" id="input1_{{$cnt}}">
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="hidden" name="shipment_detail1_{{$cnt}}" id="shipment_detail1_{{$cnt}}" class="form-control" value="{{$shipment_details->shipment_detail_id}}">
                                            <select name="product_desc1_{{$cnt}}" id="product_desc1_{{$cnt}}" class="form-control select2 validate[required]" onchange="getFnsku(1,{{$cnt}},this.value)">
                                                <option value="">Product Description</option>
                                                @foreach($product as $products)
                                                    <option value=" {{ $products->id." ".$products->FNSKU }}" @if($shipment_details->product_id==$products->id) {{ "selected" }} @endif> {{ $products->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="upc_fnsku1_{{$cnt}}" id="upc_fnsku1_{{$cnt}}" placeholder="UPC/FNSKU" class="form-control validate[required]" value="{{$shipment_details->fnsku}}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="qty_per_case1_{{$cnt}}" id="qty_per_case1_{{$cnt}}" placeholder="Qty Per Case" class="form-control validate[required]" value="{{$shipment_details->qty_per_box}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="no_of_case1_{{$cnt}}" id="no_of_case1_{{$cnt}}" placeholder="# Of Case" class="form-control validate[required]" value="{{$shipment_details->no_boxs}}" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="total1_{{$cnt}}" id="total1_{{$cnt}}" placeholder="Total" class="form-control validate[required]" value="{{$shipment_details->total}}" onfocus="get_total(1,{{$cnt}})" readonly>
                                        </div>
                                    </div>
                                    @if($cnt>1)
                                    <div class="col-md-2">
                                        <input type="button" class="btn btn-primary" id="remove1_{{$cnt}}" onclick="remove_shipment(1,{{$cnt}},{{$shipment_details->shipment_detail_id}})" value="-">
                                    </div>
                                    @endif
                                </div>
                                {{--*/$cnt++/*--}}
                            @endif
                        @endforeach
                        {{--*/ $cnt=$cnt-1 /*--}}
                        {!! Form::hidden('count1', old('count1',$cnt), ['class' => 'form-control', 'id' => 'count1']) !!}
                        {!! Form::hidden('original_count1', old('original_count1',$cnt), ['class' => 'form-control', 'id' => 'original_count1']) !!}
                    @else
                        <div class="form-group" id="label1_1">
                            {!! Form::label('product_desc1_1', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('upc_fnsku1_1', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('qty_per_case1_1', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('no_of_case1_1', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('total1_1', 'Total *', ['class' => 'control-label col-md-2']) !!}
                        </div>
                        <div class="form-group" id="input1_1">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <select name="product_desc1_1" id="product_desc1_1" class="form-control select2 validate[required]" onchange="getFnsku(1,1,this.value)">
                                        <option value="">Product Description</option>
                                        @foreach($product as $products)
                                            <option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('upc_fnsku1_1', old('upc_fnsku1_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'UPC/FNSKU', 'id' => 'upc_fnsku1_1', 'readonly'=>true]) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('qty_per_case1_1', old('qty_per_case1_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Qty Per Case', 'id'=>'qty_per_case1_1']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('no_of_case1_1', old('no_of_case1_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'# Of Case', 'id'=> 'no_of_case1_1']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('total1_1', old('total1_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Total', 'onfocus'=>'get_total(1,1)', 'readonly'=>true]) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::hidden('count1', old('count1',1), ['class' => 'form-control', 'id' => 'count1']) !!}
                        {!! Form::hidden('original_count1', old('original_count1',1), ['class' => 'form-control', 'id' => 'original_count1']) !!}
                    @endif

                </div>
                <div class="col-md-12" id="button1">
                    {!! Form::button( ' + ', ['class'=>'btn btn-primary', 'id'=>'add1', 'onclick'=>'add_shipment(1)']) !!}
                </div>
                <div class="col-md-12" id="main2" hidden>
                    <hr>
                    <div class="form-group">
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-addon">Shipment 2</span>
                            </div>
                        </div>
                        {!! Form::label('shipping_method2', 'Shipping Method *', ['class' => 'control-label col-md-2']) !!}
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <select name="shipping_method2" class="form-control select2 validate[required]">
                                    <option value="">Select Shipping Method</option>
                                    @foreach ($shipping_method as $ship_method)
                                        <option value="{{ $ship_method->shipping_method_id }}" @if(count($shipment)>1) @if($shipment[1]->shipping_method_id==$ship_method->shipping_method_id){{"selected"}} @endif @endif >  {{ $ship_method->shipping_name }}</option>
                                    @endforeach
                                </select>
                                {!! Form::hidden('shipment_id2', old('shipment_id2', count($shipment)>1 ? $shipment[1]->shipment_id  : null), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    @if(count($shipment)>1)
                        {{--*/ $cnt=1 /*--}}
                        @foreach($shipment_detail as $shipment_details)
                            @if($shipment[1]->shipment_id==$shipment_details->shipment_id)
                                <div class="form-group" id="label2_{{$cnt}}">
                                    {!! Form::label('product_desc2_1', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('upc_fnsku2_1', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('qty_per_case2_1', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('no_of_case2_1', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                                    {!! Form::label('total2_1', 'Total *', ['class' => 'control-label col-md-2']) !!}
                                </div>
                                <div class="form-group" id="input2_{{$cnt}}">
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="hidden" name="shipment_detail2_{{$cnt}}" id="shipment_detail2_{{$cnt}}" class="form-control" value="{{$shipment_details->shipment_detail_id}}">
                                            <select name="product_desc2_{{$cnt}}" id="product_desc2_{{$cnt}}" class="form-control select2 validate[required]" onchange="getFnsku(2,{{$cnt}},this.value)">
                                                <option value="">Product Description</option>
                                                @foreach($product as $products)
                                                    <option value=" {{ $products->id." ".$products->FNSKU }}" @if($shipment_details->product_id==$products->id) {{ "selected" }} @endif> {{ $products->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="upc_fnsku2_{{$cnt}}" id="upc_fnsku2_{{$cnt}}" placeholder="UPC/FNSKU" class="form-control validate[required]" value="{{$shipment_details->fnsku}}"  readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="qty_per_case2_{{$cnt}}" id="qty_per_case2_{{$cnt}}" placeholder="Qty Per Case" class="form-control validate[required]" value="{{$shipment_details->qty_per_box}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="no_of_case2_{{$cnt}}" id="no_of_case2_{{$cnt}}" placeholder="# Of Case" class="form-control validate[required]" value="{{$shipment_details->no_boxs}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-addon"></span>
                                            <input type="text" name="total2_{{$cnt}}" id="total2_{{$cnt}}" placeholder="Total" class="form-control validate[required]" value="{{$shipment_details->total}}" onfocus="get_total(2,{{$cnt}})" readonly>
                                        </div>
                                    </div>
                                    @if($cnt>1)
                                    <div class="col-md-2">
                                    <input type="button" class="btn btn-primary" id="remove2_{{$cnt}}" onclick="remove_shipment(2,{{$cnt}},{{$shipment_details->shipment_detail_id}})" value="-">
                                    </div>
                                    @endif
                                </div>
                                {{--*/ $cnt++ /*--}}
                            @endif
                        @endforeach
                        {{--*/ $cnt=$cnt-1 /*--}}
                        {!! Form::hidden('count2', old('count2',$cnt), ['class' => 'form-control', 'id' => 'count2']) !!}
                        {!! Form::hidden('original_count2', old('original_count2',$cnt), ['class' => 'form-control', 'id' => 'original_count2']) !!}
                    @else
                        <div class="form-group" id="label2_1">
                            {!! Form::label('product_desc2_1', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('upc_fnsku2_1', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('qty_per_case2_1', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('no_of_case2_1', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('total2_1', 'Total *', ['class' => 'control-label col-md-2']) !!}
                        </div>
                        <div class="form-group" id="input2_1">
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    <select name="product_desc2_1" id="product_desc2_1" class="form-control select2 validate[required]" onchange="getFnsku(2,1,this.value)">
                                        <option value="">Product Description</option>
                                        @foreach($product as $products)
                                            <option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('upc_fnsku2_1', old('upc_fnsku2_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'UPC/FNSKU', 'id' => 'upc_fnsku2_1', 'readonly'=>true]) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('qty_per_case2_1', old('qty_per_case2_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Qty Per Case', 'id'=>'qty_per_case2_1']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('no_of_case2_1', old('no_of_case2_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'# Of Case','id'=>'no_of_case2_1']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('total2_1', old('total2_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Total', 'onfocus'=>'get_total(2,1)', 'readonly'=>true]) !!}
                                </div>
                            </div>
                        </div>

                        {!! Form::hidden('count2', old('count2',1), ['class' => 'form-control', 'id' => 'count2']) !!}
                        {!! Form::hidden('original_count2', old('original_count2',1), ['class' => 'form-control', 'id' => 'original_count2']) !!}
                    @endif
                </div>
                <div class="col-md-12" id="button2" hidden>
                    {!! Form::button( ' + ', ['class'=>'btn btn-primary', 'id'=>'add2', 'onclick'=>'add_shipment(2)']) !!}
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-9">
                            {!! Form::submit('  Next  ', ['class'=>'btn btn-primary', ]) !!}
                        </div>
                    </div>
                </div>
            </div><!-- .row -->
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
    <script type="text/javascript">
        function getFnsku(no,sub_no,id)
        {
            fnsku=id.split(' ');
            $('#upc_fnsku'+no+"_"+sub_no).val(fnsku[2]);
        }
        function add_shipment(no)
        {
            sub_cnt=$("#count"+no).val();
            cnt=parseInt(sub_cnt)+1;
            $('#main'+no).append('<div class="form-group" id="label'+no+'_'+cnt+'">{!! Form::label("product_desc'+no+'_'+cnt+'", "Product Description *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("upc_fnsku'+no+'_'+cnt+'", "UPC/FNSKU *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("qty_per_case'+no+'_'+cnt+'", "Qty Per Case *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("no_of_case'+no+'_'+cnt+'", "# Of Case *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("total'+no+'_'+cnt+'", "Total *", ["class" => "control-label col-md-2"]) !!}</div><div class="form-group" id="input'+no+'_'+cnt+'"><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><select name="product_desc'+no+'_'+cnt+'" id="product_desc'+no+'_'+cnt+'" class="form-control select2 validate[required]" onchange="getFnsku('+no+','+cnt+',this.value)"><option value="">Product Description</option>@foreach($product as $products)<option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product_name }}</option>@endforeach</select></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="upc_fnsku'+no+'_'+cnt+'" class = "form-control validate[required]" placeholder="UPC/FNSKU" id="upc_fnsku'+no+'_'+cnt+'" readonly></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="qty_per_case'+no+'_'+cnt+'" class = "form-control validate[required]" placeholder="Qty Per Case" id="qty_per_case'+no+'_'+cnt+'"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="no_of_case'+no+'_'+cnt+'" class = "form-control validate[required]" placeholder="# Of Case" id="no_of_case'+no+'_'+cnt+'"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="total'+no+'_'+cnt+'" class = "form-control validate[required]" placeholder="Total" id="total'+no+'_'+cnt+'" onfocus="get_total('+no+','+cnt+')" readonly></div></div><div class="col-md-2"><input type="button" class="btn btn-primary" id="remove'+no+'_'+cnt+'" onclick="remove_shipment('+no+','+cnt+')" value="-"></div></div>');
            $('#count'+no).val(cnt);
            tmp=parseInt($('#original_count'+no).val())+1;
            $('#original_count'+no).val(tmp);
            if($('#original_count'+no).val()>=4)
            {
                $('#button'+no).hide();
            }
            else
            {
                $('#button'+no).show();
            }
            cnt++;
        }
        function get_total(no,sub_no) {
            total=$('#qty_per_case'+no+"_"+sub_no).val()*$('#no_of_case'+no+"_"+sub_no).val();
            $('#total'+no+"_"+sub_no).val(total);
        }
        $(document).ready(function () {
            $('.datepicker').datepicker( {
            });
        });
        function get_Split_shipment(value)
        {
            if(value=="1")
            {
                $("#main2").show();
                $("#button2").show();
                $('#ship_count').val(2);
            }
            else
            {
                $("#main2").hide();
                $("#button2").hide();
                $('#ship_count').val(1);
            }
        }
        $(document).ready(function () {
            value=$('#split_sphiment').val();
            if(value=="1")
            {
                $("#main2").show();
                $("#button2").show();
                $('#ship_count').val(2);
            }
            else
            {
                $("#main2").hide();
                $("#button2").hide();
                $('#ship_count').val(1);
            }
            count1=$("#original_count1").val();
            count2=$("#original_count2").val();
            if(count1>=4)
            {
                $("#button1").hide();
            }
            if(count2>=4)
            {
                $("#button2").hide();
            }

        });
        function remove_shipment(no,sub_no,id)
        {
            $("#label"+no+"_"+sub_no).remove();
            $("#input"+no+"_"+sub_no).remove();

            $.ajax({
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/removeproduct', // This is the url we gave in the route
                data: {
                    'shipment_detail_id': id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    alert("product deleted Successfully");

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
            tmp=parseInt($('#original_count'+no).val())-1;
            $('#original_count'+no).val(tmp);
            if($('#original_count'+no).val()>=4)
            {
                $('#button'+no).hide();
            }
            else
            {
                $('#button'+no).show();
            }
        }
    </script>
@endsection
