@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/billofladingform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('order_id', old('order_id', isset($order_id)?$order_id:null), ['class' => 'form-control']) !!}
            <div class="col-md-6">
                FBAforward PO Number : {{isset($user)? $user[0]->order_no :null}}
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
                                <th class="col-md-6"><span>Product</span></th>
                                <th><span>Shipment Method</span></th>
                            </tr>
                            </thead>
                            <tbody>
                           @foreach($shipment_detail as $shipment_details)
                                @if($shipment_details->shipment_id==$shipments->shipment_id)
                                    <tr>
                                        <td>{{ $shipment_details->product_name }}</td>
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
                                {!! Form::label('ref_number', 'Shipment Reference Number (SB Number) *', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="ref_number{{$cnt}}" id="ref_number{{$cnt}}" class="form-control" placeholder="Shipment Reference Number (SB Number)">
                                    </div>
                                </div>
                                {!! Form::label('bill', 'Bill of Lading *', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="file" name="bill{{$cnt}}" id="bill{{$cnt}}"  placeholder="Bill of Lading">
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
