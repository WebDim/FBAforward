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
                        <th><span>Product</span></th>
                        <th><span>Quantity</span></th>
                        <th><span>Prep Services</span></th>
                        <th><span>Total</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/ $cnt = 1 /*--}}
                    {{--*/ $grand_total = 0 /*--}}
                    @foreach($product as $products)
                        {{--*/ $total=0 /*--}}
                        <tr>
                            <td><input type="text" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                                <b class="text-info">{{ $products->product_name }}</b></td>
                            <td><input type="hidden" name="qty{{ $cnt }}" value="{{ $products->total }}">
                                <b class="text-info">{{ $products->total }}</b></td>
                            <td><b class="text-info">

                                    @foreach ($prep_service as $prep_services)
                                        <input type="checkbox" name="service{{$cnt}}[]" id="service{{$cnt}}[]" value="{{ $prep_services->prep_service_id }}" >{{ $prep_services->service_name }}
                                           {{--*/ $total = $total+$prep_services->price /*--}}
                                        <br>
                                    @endforeach

                            </b></td>
                            <td><input type="hidden" name="total{{ $cnt }}" value="{{ $total }}"><b class="text-info">{{ $total }}</b></td>
                            {{--*/ $grand_total =$grand_total+$total /*--}}
                        </tr>
                        {{--*/ $cnt++ /*--}}
                    @endforeach
                    <tr>
                        <td></td>
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
    </script>
@endsection