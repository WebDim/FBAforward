@extends('layouts.frontend.app')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-10">
                <h2 class="page-head-line">{{$title}}</h2>
            </div>
            <div class="col-md-2 ">
                <a href="{{ url()->previous() }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'order/deliverybookingform', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
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
                    <div class="col-md-6"><h4>Shipment </h4></div>
                    <div class="col-md-6"><h4>{{$shipments->shipping_name}}</h4></div>
                    <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{$shipments->shipment_id}}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('CFS_terminal', 'CFS Terminal<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="CFS_terminal{{$cnt}}" id="CFS_terminal{{$cnt}}" class="form-control validate[required]" onchange="show_terminal(this.value)">
                                            <option value="">Select CFS Terminal</option>
                                            <option value="">Add New</option>
                                            @foreach($cfs_terminal as $cfs_terminals)
                                            <option value="{{$cfs_terminals->id}}">{{$cfs_terminals->terminal_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('trucking_company', 'Trucking Company<span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="trucking_company{{$cnt}}" id="trucking_company{{$cnt}}" class="form-control validate[required]" onchange="show_trucking(this.value)">
                                            <option value="">Select Trucking Company</option>
                                            <option value="">Add New</option>
                                            @foreach($trucking_company as $trucking_compnays)
                                            <option value="{{$trucking_compnays->id}}">{{$trucking_compnays->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('warehouse_fee', 'Warehouse Fees <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="warehouse_fee{{$cnt}}" id="warehouse_fee{{$cnt}}" class="form-control validate[required]" placeholder="Warehouse Fees">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('fee_paid', 'Fees Paid <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="fee_paid{{$cnt}}" id="fee_paid{{$cnt}}" class="form-control validate[required]">
                                            <option value="">Fees Paid</option>
                                            @foreach($payment_type as $payment_types)
                                            <option value="{{$payment_types->id}}">{{$payment_types->type_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('ETA_warehouse', 'ETA Warehouse <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="ETA_warehouse{{$cnt}}" id="ETA_warehouse{{$cnt}}" class="form-control datepicker validate[required]" placeholder="ETA Warehouse ">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('delivery_destination', 'Delivery Destination <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <select name="delivery_destination{{$cnt}}" id="delivery_destination{{$cnt}}" class="form-control validate[required]" onchange="show_destination(this.value)">
                                            <option value="">Select Delivery Destination</option>
                                            <option value="">Add New</option>
                                            @foreach($delivery_destination as $delivery_destinations)
                                                <option value="{{$delivery_destinations->id}}">{{$delivery_destinations->destination_name}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! htmlspecialchars_decode(Form::label('last_free_day', 'Last Free Day <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="last_free_day{{$cnt}}" id="last_free_day{{$cnt}}" class="form-control datepicker validate[required]" placeholder="Last Free Day ">
                                    </div>
                                </div>
                                {!! htmlspecialchars_decode(Form::label('pallet_exchange', 'Pallet Exchange <span class="required">*</span> ', ['class' => 'control-label col-md-2'])) !!}
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" name="pallet_exchange{{$cnt}}" id="pallet_exchange{{$cnt}}" class="form-control validate[required]" placeholder="Pallet Exchange ">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{--*/$cnt++/*--}}
            @endforeach
            <input type="hidden" name="count" id="count" value="{{$cnt}}">
            <div id="terminal" hidden>
                <hr>
                <h4>Add New CFS Terminal</h4>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('terminal_name', 'Terminal Name<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                        <div class="col-md-4">
                            <div class="">
                                <span class=""></span>
                                {!! Form::text('terminal_name', old('terminal_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Terminal Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::button( 'Add CFS Terminal', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'addterminal()']) !!}
                    </div>
                </div>
            </div>
            <div id="trucking" hidden>
                <hr>
                <h4>Add New Trucking Company</h4>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('company_name', 'Company Name<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                        <div class="col-md-4">
                            <div class="">
                                <span class=""></span>
                                {!! Form::text('company_name', old('company_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Company Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::button( 'Add Trucking Company', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'addtrucking()']) !!}
                    </div>
                </div>
            </div>
            <div id="destination" hidden>
                <hr>
                <h4>Add New Delivery Destination</h4>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! htmlspecialchars_decode(Form::label('destination_name', 'Delivery Destination Name<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
                        <div class="col-md-4">
                            <div class="">
                                <span class=""></span>
                                {!! Form::text('destination_name', old('destination_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Delivery Destination Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::button( 'Add Delivery Destination', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'adddestination()']) !!}
                    </div>
                </div>
            </div>
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
    {!! Html::script('https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js') !!}
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
        $(document).ready(function () {
            $('.datepicker').datepicker( {
            });
        });
        function show_trucking(value) {
            if(value == "")
            {
                $('#trucking').show();
            }
            else
            {
                $('#trucking').hide();
            }
        }
        function show_terminal(value) {
            if(value == "")
            {
                $('#terminal').show();
            }
            else
            {
                $('#terminal').hide();
            }
        }
        function show_destination(value) {
            if(value == "")
            {
                $('#destination').show();
            }
            else
            {
                $('#destination').hide();
            }
        }
        function addtrucking() {
            company_name=$('#company_name').val();
            $("#validate").validate({
                rules: {
                    "company_name": {
                        required: true,

                    },
                },
                messages:{
                    "company_name": {
                        required:"<span class='required'>Company name is required</span>",
                    },
                }
            });
            if($('#validate').valid()) {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/addtrucking', // This is the url we gave in the route
                    data: {
                        'company_name': company_name,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        console.log(response);
                        swal("Company added Successfully");
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        function addterminal() {
            terminal_name=$('#terminal_name').val();
            $("#validate").validate({
                rules: {
                    "terminal_name": {
                        required: true,

                    },
                },
                messages:{
                    "terminal_name": {
                        required:"<span class='required'>Terminal name is required</span>",
                    },
                }
            });
            if($('#validate').valid()) {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/addterminal', // This is the url we gave in the route
                    data: {
                        'terminal_name': terminal_name,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        console.log(response);
                        swal("CFS Terminal added Successfully");
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        function adddestination() {
            destination_name=$('#destination_name').val();
            $("#validate").validate({
                rules: {
                    "destination_name": {
                        required: true,

                    },
                },
                messages:{
                    "destination_name": {
                        required:"<span class='required'>Destination name is required</span>",
                    },
                }
            });
            if($('#validate').valid()) {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/adddestination', // This is the url we gave in the route
                    data: {
                        'destination_name': destination_name,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        console.log(response);
                        swal("Delivery Destination added Successfully");
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
    </script>
@endsection
