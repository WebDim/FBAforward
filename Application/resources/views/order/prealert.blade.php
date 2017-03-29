@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/prealertform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('order_id', old('order_id', isset($order_id)?$order_id:null), ['class' => 'form-control']) !!}
            <div class="col-md-6">
                FBAforward order # : {{isset($user)? $user[0]->order_no :null}}
            </div>
            <div class="col-md-6">
                Customer Email : {{isset($user)? $user[0]->contact_email :null}}
            </div>
            {{--*/$cnt=1/*--}}
            @foreach($shipment as $key=>$shipments)
                <div>
                    <div class="col-md-6"><h4>Shipment # {{$key+1}} </h4></div>
                    <div class="col-md-6"><h4>{{$shipments->shipping_name}}</h4></div>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                @if($shipments->shipping_name=='sea' || $shipments->shipping_name=='Sea' || $shipments->shipping_name=='SEA')
                                {!! Form::label('ISF', 'ISF ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="ISF{{$cnt}}" id="ISF{{$cnt}}"  placeholder="ISF ">
                                    </div>
                                @endif
                                </div>
                                {!! Form::label('HBL', 'HBL/HAWB ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="HBL{{$cnt}}" id="HBL{{$cnt}}"  placeholder="HBL/HAWB">
                                    </div>
                                </div>
                                {!! Form::label('MBL', 'MBL/MAWB ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="file" name="MBL{{$cnt}}" id="MBL{{$cnt}}"  placeholder="MBL/MAWB">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('ETD_china', 'ETD China ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="ETD_china{{$cnt}}" id="ETD_china{{$cnt}}"  class="form-control" placeholder="ETD China ">
                                    </div>
                                </div>
                                {!! Form::label('ETA_US', 'ETA U.S. ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="ETA_US{{$cnt}}" id="ETA_US{{$cnt}}"  class="form-control" placeholder="ETA U.S. ">
                                    </div>
                                </div>
                                {!! Form::label('delivery_port', 'Delivery Port ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="delivery_port{{$cnt}}" id="delivery_port{{$cnt}}" class="form-control">
                                            <option value="">Delivery Port</option>
                                            <option value="San Diego">San Diego</option>
                                            <option value="Long Beach">Long Beach</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('vessel', 'Vessel ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="vessel{{$cnt}}" id="vessel{{$cnt}}"  class="form-control" placeholder="Vessel ">
                                    </div>
                                </div>
                                {!! Form::label('container', 'Container # ', ['class' => 'control-label col-md-2']) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="container{{$cnt}}" id="container{{$cnt}}"  class="form-control" placeholder="Container # ">
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
