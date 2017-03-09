@extends('layouts.frontend.app')

@section('title', 'Pre Inspection')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'pre_inspection'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/preinspection', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th class="col-md-2"><span>Suppliers</span></th>
                        <th class="col-md-5"><span>Product</span></th>
                        <th class="col-md-2"><span>Quantity</span></th>
                        <th class="col-md-3"><span>Inspection</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    @foreach($supplier as $suppliers)
                        {{--*/ $product_cnt = 1 /*--}}
                        <tr>
                            <td class="col-md-2"><input type="hidden" name="supplier_id{{ $cnt }}" value="{{ $suppliers->supplier_id }}">
                                <b class="text-info">{{ $suppliers->company_name }}</b></td>
                            <td hidden>
                                @foreach($product as $products)
                                    @if($products->supplier_id==$suppliers->supplier_id)
                                        <input type="hidden" name="supplier_inspection_id{{$cnt."_".$product_cnt}}" value="{{$products->supplier_inspection_id }}">
                                        <input type="hidden" name="supplier_detail_id{{$cnt."_".$product_cnt}}" value="{{$products->supplier_detail_id}}"><br>
                                        <input type="hidden" id="order_id" name="order_id" value="{{$products->order_id}}">
                                        {{--*/ $product_cnt++ /*--}}
                                    @endif
                                @endforeach
                            </td>
                            <td class="col-md-4"><b class="text-info">
                                @foreach($product as $products)
                                    @if($products->supplier_id==$suppliers->supplier_id)
                                            @if($products->product_nick_name==''){{ $products->product_name}} @else {{$products->product_nick_name}} @endif<br>
                                    @endif
                                @endforeach
                                </b></td>
                            <td class="col-md-2"><b class="text-info">
                                    @foreach($product as $products)
                                        @if($products->supplier_id==$suppliers->supplier_id)
                                            {{ $products->total_unit }}<br>
                                        @endif
                                    @endforeach
                                </b></td>
                            <td class="col-md-3"><b class="text-info">
                                    <select name="inspection{{ $cnt }}" id="inspection{{ $cnt }}" class="form-control select2 validate[required]" onchange="add_Inspection({{ $cnt }}, this.value)">
                                        <option value="0" @if($suppliers->is_inspection=='0') {{ "selected" }} @endif>No</option>
                                        <option value="1" @if($suppliers->is_inspection=='1') {{ "selected" }} @endif>Yes</option>
                                    </select>
                                    <br>
                            <div  @if($suppliers->is_inspection=='0' || $suppliers->is_inspection=='')  {{ "hidden" }} @endif  id="desc_div{{ $cnt }}">
                               {!! htmlspecialchars_decode(Form::label('inspection_desc{{ $cnt }}', 'Inspection Instruction <span class="required">*</span> ', ['class' => 'control-label'])) !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <textarea name="inspection_desc{{ $cnt }}" id="inspection_desc{{ $cnt }}" cols="20" rows="3" class=" form-control validate[required]">{{ $suppliers->inspection_decription }}</textarea>
                                    </div>
                                </div>

                                </b></td>
                        </tr>
                        <input type="hidden" name="product_count{{$cnt}}" id="product_count{{$cnt}}" value="{{$product_cnt}}">
                       {{--*/ $cnt++ /*--}}
                    @endforeach
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="count" id="count" value="{{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        <a href="{{ URL::route('supplierdetail') }}" class="btn btn-primary">Previous</a>
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
    <script>
        function add_Inspection(no,value) {
            if(value=='0')
            {
                $('#desc_div'+no).hide();
                $('#inspection_desc'+no).val('');
            }
            else
            {
                $('#desc_div'+no).show();
            }
        }

    </script>
@endsection