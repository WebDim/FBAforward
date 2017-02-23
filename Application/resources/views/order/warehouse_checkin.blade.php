@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/warehousecheckinform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('order_id', old('order_id', isset($order_id)?$order_id:null), ['class' => 'form-control']) !!}
            <div class="col-md-12">
                FBAforward order # : {{isset($user)? $user[0]->order_no :null}}
            </div>
            <div class="col-md-6">
                Customer Business Name : {{isset($user)? $user[0]->company_name :null}}
            </div>
            <div class="col-md-6">
                Customer Email : {{isset($user)? $user[0]->contact_email :null}}
            </div>
            {{--*/$cnt=1/*--}}
            @foreach($shipment as $key=>$shipments)
                <div>
                    <h4>Shipment # {{$key+1}} </h4>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="table-responsive no-padding">
                        <table class="table" id="list">
                            <thead>
                            <tr>
                                <th class="col-md-2"><span>Product</span></th>
                                <th class="col-md-2"><span>Qty Per Case</span></th>
                                <th class="col-md-2"><span># Of Case</span></th>
                                <th><span>Total</span></th>
                                <th><span>Shipment Type</span></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($shipment_detail as $shipment_details)
                                @if($shipment_details->shipment_id==$shipments->shipment_id)
                                    <tr>
                                        <td>{{ $shipment_details->product_name }}</td>
                                        <td>{{ $shipment_details->qty_per_box}}</td>
                                        <td>{{ $shipment_details->no_boxs}}</td>
                                        <td>{{ $shipment_details->total}}</td>
                                        <td>{{ $shipment_details->shipping_name }}</td>
                                    </tr>

                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('cartoon_length', 'Cartoon Length *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_length{{$cnt}}" id="cartoon_length{{$cnt}}" class="form-control" placeholder="Cartoon Length">
                                    </div>
                                </div>
                                {!! Form::label('cartoon_width', 'Cartoon Width *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_width{{$cnt}}" id="cartoon_width{{$cnt}}" class="form-control" placeholder="Cartoon Width">
                                    </div>
                                </div>
                                {!! Form::label('cartoon_weight', 'Cartoon Weight *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_weight{{$cnt}}" id="cartoon_weight{{$cnt}}" class="form-control" placeholder="Cartoon Weight">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('cartoon_height', 'Cartoon Height *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_height{{$cnt}}" id="cartoon_height{{$cnt}}" class="form-control" placeholder="Cartoon Height">
                                    </div>
                                </div>
                                {!! Form::label('no_of_cartoon', '# Of Cartoon *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="no_of_cartoon{{$cnt}}" id="no_of_cartoon{{$cnt}}" class="form-control" placeholder="# Of Cartoon">
                                    </div>
                                </div>
                                {!! Form::label('unit_per_cartoon', 'Unit Per Cartoon *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="unit_per_cartoon{{$cnt}}" id="unit_per_cartoon{{$cnt}}" class="form-control" placeholder="Unit Per Cartoon">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('cartoon_condition', 'Cartoon Condition *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="cartoon_condition{{$cnt}}" id="cartoon_condition{{$cnt}}" class="form-control" placeholder="Cartoon Condition">
                                    </div>
                                </div>
                                {!! Form::label('images', 'Upload Image *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="images{{$cnt}}[]" id="images{{$cnt}}[]"  multiple placeholder="Upload Image">
                                    </div>
                                </div>
                                {!! Form::label('location', 'Warehouse Location *', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                       <select name="location{{$cnt}}" id="location{{$cnt}}" class="form-control">
                                           <option value="">Select Warehouse Location</option>
                                           <option value="1">test</option>
                                       </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--*/$cnt++/*--}}
            @endforeach
            <input type="hidden" name="count" id="count" value="{{$cnt}}">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-9">
                        {!! Form::submit('  Submit   ', ['class'=>'btn btn-primary', ]) !!}
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
