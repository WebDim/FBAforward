@extends('layouts.frontend.app')

@section('title', 'Listing Services Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'list_service'])
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Listing Services Information</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/listservice', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <td><span>Product</span></td>
                        <td><span>Listing Services</span></td>
                        <td><span>Total</span></td>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    {{--*/ $grand_total = 0 /*--}}
                    @foreach($product as $products)
                        <tr>
                            <td><input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <b class="text-info">{{ $products->product_name }}</b></td>
                            <td><b class="text-info">
                                    @foreach ($list_service as $list_services)
                                        <input type="checkbox" name="service{{$cnt}}[]" id="service{{$cnt}}[]" value="{{ $list_services->listing_service_id }}">{{ $list_services->service_name }}<br>
                                    @endforeach
                                </b></td>
                            <td><input type="hidden" name="total{{ $cnt }}" value="{{ $products->total }}"><b class="text-info">{{ $products->total }}</b></td>
                            {{--*/ $grand_total =$grand_total+$products->total /*--}}
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td><input type="hidden" name="grand_total" value="{{ $grand_total }}">{{ $grand_total }}</td>
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
    </script>
@endsection