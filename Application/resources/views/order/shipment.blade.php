@extends('layouts.frontend.app')

@section('title', 'Shipment call')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="page-head-line">Shipment Information</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        {!! Form::open(['url' =>  'order/shipment', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
        {!! Form::hidden('count', old('count',2), ['class' => 'form-control', 'id' => 'count']) !!}

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('split_shipment', 'Split Shipment *', ['class' => 'control-label col-md-3']) !!}
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <select name="split_shipment" class="form-control select2 validate[required]">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="2"> No</option>
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
                            {!! Form::text('date', old('date'), ['class' => 'datepicker form-control validate[required]', 'placeholder'=>'Goods Ready Date']) !!}
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
                               <option value="">Shipping Method</option>
                                  @foreach ($shipping_method as $ship_method)
                                      <option value="{{ $ship_method->shipping_method_id }}">  {{ $ship_method->shipping_name }}</option>
                                  @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('product_desc1', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('upc_fnsku1', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('qty_per_case1', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('no_of_case1', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('total1', 'Total *', ['class' => 'control-label col-md-2']) !!}
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <select name="product_desc1" id="product_desc1" class="form-control select2 validate[required]" onchange="getFnsku(1,this.value)">
                                <option value="">Product Description</option>
                                @foreach($product as $products)
                                    <option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('upc_fnsku1', old('upc_fnsku1'), ['class' => 'form-control validate[required]', 'placeholder'=>'UPC/FNSKU', 'id' => 'upc_fnsku1']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('qty_per_case1', old('qty_per_case1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Qty Per Case', 'id'=>'qty_per_case1']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('no_of_case1', old('no_of_case1'), ['class' => 'form-control validate[required]', 'placeholder'=>'# Of Case', 'id'=> 'no_of_case1']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('total1', old('total1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Total', 'onfocus'=>'get_total(1)']) !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-12" id="main2">
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
                                <option value="">Shipping Method</option>
                                @foreach ($shipping_method as $ship_method)
                                    <option value="{{ $ship_method->shipping_method_id }}">  {{ $ship_method->shipping_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('product_desc2', 'Product Description *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('upc_fnsku2', 'UPC/FNSKU *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('qty_per_case2', 'Qty Per Case *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('no_of_case2', '# Of Case *', ['class' => 'control-label col-md-2']) !!}
                    {!! Form::label('total2', 'Total *', ['class' => 'control-label col-md-2']) !!}
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <select name="product_desc2" id="product_desc2" class="form-control select2 validate[required]" onchange="getFnsku(2,this.value)">
                                <option value="">Product Description</option>
                                @foreach($product as $products)
                                    <option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('upc_fnsku2', old('upc_fnsku2'), ['class' => 'form-control validate[required]', 'placeholder'=>'UPC/FNSKU', 'id' => 'upc_fnsku2']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('qty_per_case2', old('qty_per_case2'), ['class' => 'form-control validate[required]', 'placeholder'=>'Qty Per Case', 'id'=>'qty_per_case2']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('no_of_case2', old('no_of_case2'), ['class' => 'form-control validate[required]', 'placeholder'=>'# Of Case','id'=>'no_of_case2']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            {!! Form::text('total2', old('total2'), ['class' => 'form-control validate[required]', 'placeholder'=>'Total', 'onfocus'=>'get_total(2)']) !!}
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::button( ' + ', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'add_shipment()']) !!}
                    <div class="col-md-9 col-md-offset-9">
                        {!! Form::submit('  Next  ', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
            </div>
        </div><!-- .row -->
        {!! Form::close() !!}
    </div>
</div>
@endsection
@section('js')
    {!! Html::style('http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css') !!}
    {!! Html::script("http://code.jquery.com/jquery-1.9.1.js") !!}
    {!! Html::script("http://code.jquery.com/ui/1.11.0/jquery-ui.js") !!}
    {!! Html::script("http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js") !!}
    {!! Html::script("http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js") !!}
    <script type="text/javascript">
        function getFnsku(no,id)
        {
            fnsku=id.split(' ');
            $('#upc_fnsku'+no).val(fnsku[2]);
        }
        function add_shipment()
        {
            cnt=$('#count').val();
            no=parseInt(cnt)+1;
            $('#main'+cnt).after('<div class="col-md-12" id="main'+no+'"><hr><div class="form-group"><div class="col-md-2"><div class="input-group"><span class="input-group-addon">Shipment '+no+'</span></div></div>{!! Form::label("shipping_method'+no+'", "Shipping Method *", ["class" => "control-label col-md-2"]) !!}<div class="col-md-3"><div class="input-group"><span class="input-group-addon"></span><select name="shipping_method'+no+'" class="form-control select2 validate[required]"><option value="">Shipping Method</option>@foreach ($shipping_method as $ship_method)<option value="{{ $ship_method->shipping_method_id }}">  {{ $ship_method->shipping_name }}</option>@endforeach</select></div></div></div><div class="form-group">{!! Form::label("product_desc'+no+'", "Product Description *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("upc_fnsku'+no+'", "UPC/FNSKU *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("qty_per_case'+no+'", "Qty Per Case *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("no_of_case'+no+'", "# Of Case *", ["class" => "control-label col-md-2"]) !!}{!! Form::label("total'+no+'", "Total *", ["class" => "control-label col-md-2"]) !!}</div><div class="form-group"><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><select name="product_desc'+no+'" id="product_desc'+no+'" class="form-control select2 validate[required]" onchange="getFnsku('+no+',this.value)"><option value="">Product Description</option>@foreach($product as $products)<option value=" {{ $products->id." ".$products->FNSKU }}"> {{ $products->product }}</option>@endforeach</select></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="upc_fnsku'+no+'" class = "form-control required" placeholder="UPC/FNSKU" id="upc_fnsku'+no+'"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="qty_per_case'+no+'" class = "form-control required" placeholder="Qty Per Case" id="qty_per_case'+no+'"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="no_of_case'+no+'" class = "form-control required" placeholder="# Of Case" id="no_of_case'+no+'"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon"></span><input type="text" name="total'+no+'" class = "form-control required" placeholder="Total" id="total'+no+'" onfocus="get_total('+no+')"></div></div></div></div>');
            cnt++;
            $('#count').val(cnt);
        }
        function get_total(no) {
            total=$('#qty_per_case'+no).val()*$('#no_of_case'+no).val();
            $('#total'+no).val(total);
        }
        $(document).ready(function () {

            $('.datepicker').datepicker( {

            });
        });
    </script>
@endsection
