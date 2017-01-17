@extends('layouts.frontend.app')

@section('title', 'Product Labels Information')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Product Labels Information</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/productlabels', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <td><span>Product</span></td>
                        <td><span>SKU</span></td>
                        <td><span>Quantity</span></td>
                        <td><span>Who Will Label</span></td>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    @foreach($product as $products)
                        <tr>
                            <td><input type="text" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id  }}">
                                <input type="text" name="product_label_detail_id{{ $cnt }}" value="{{ $products->product_label_detail_id  }}">
                                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <b class="text-info">{{ $products->product_name }}</b></td>
                            <td><input type="hidden" name="sku{{ $cnt }}" value="{{ $products->sellerSKU }}">
                                <b class="text-info">{{ $products->sellerSKU }}</b></td>
                            <td><input type="hidden" name="total{{ $cnt }}" value="{{ $products->total }}"><b class="text-info">{{ $products->total }}</b></td>
                            <td><b class="text-info">
                                    <select name="labels{{ $cnt }}" class="form-control select2 validate[required]">
                                        <option value="">Labels</option>
                                        @foreach ($product_label as $product_labels)
                                            <option value="{{ $product_labels->product_label_id }}" @if($products->product_label_id==$product_labels->product_label_id) {{ "selected" }}@endif>  {{ $product_labels->label_name }}</option>
                                        @endforeach
                                    </select>
                                </b></td>
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="count" value=" {{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        <a href="{{ URL::route('preinspection') }}" class="btn btn-primary">Previous</a>
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