@extends('layouts.frontend.app')
@section('title', 'Order Payment')
@section('content')
    @include('layouts.frontend.tabs', ['data' => 'payment'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => 'payment/update', 'method' => 'PUT', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="col-md-6">
                <div class="form-group">
                    {{--                    {!! Form::label('pre_ship_inspect', 'Pre Shipment Inspection', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Pre Shipment Inspection</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('pre_ship_inspect', old('pre_ship_inspect',$price['pre_shipment_inspection']), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Pre Shipment Inspection','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('shipping_cost', 'Shipping Cost', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Shipping Cost</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('shipping_cost', old('shipping_cost',$price['shipping_cost']), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Shipping Cost','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--{!! Form::label('port_fees', 'Port Fees', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Port Fees</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('port_fees', old('port_fees',$price['port_fee']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Port Fees','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--{!! Form::label('custom_brokerage', 'Custom Brokerage', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Custom Brokerage</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_brokerage', old('custom_brokerage',$price['custom_brokerage']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Custom Brokerage','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('custom_duty', 'Custom Duty', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Custom Duty</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_duty', old('custom_duty',$price['custom_duty']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Custom Duty','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>

                {!! Form::label('Fbaforward_Services', 'FBAFORWARD SERVICES:', ['class' => 'control-label']) !!}
                <div class="form-group">
                    {{--                    {!! Form::label('consulting', 'Consulting Charge', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Consulting Charge</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('consulting', old('consulting',$price['consult_charge']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Consulting Charge','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('label_charge', 'Label Charge', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Label Charge</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('label_charge', old('label_charge',$price['label_charge']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Label Charge','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('prep_forward', 'Prep Forwarding', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Prep Forwarding</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('prep_forward', old('prep_forward',$price['prep_forwarding']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Prep Forwarding','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('listing_service', 'Listing Services', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Listing Services</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('listing_service', old('listing_service',$price['listing_service']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Listing Services','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('total_fbaforward', 'Total Fbaforward', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Total Fbaforward</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('total_fbaforward', old('total_fbaforward'),['class' => 'form-control validate[required, custom[number]', 'placeholder'=>'Total Fbaforward','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('inbound_shipping', 'Inbound Shipping', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Inbound Shipping</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('inbound_shipping', old('inbound_shipping',$price['inbound_shipping']),['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Inbound Shipping','onblur'=>'gettotal()','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('total_cost', 'Total Cost', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Total Cost</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('total_cost', old('total_cost'),['class' => 'form-control validate[required, custom[number]]', 'id'=>'total_cost', 'placeholder'=>'Total Cost','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-7">
                        <div class="input-group">
                            <label class="control-label">* Check Back For Update Totals</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! Form::label('', 'Total Due Today', ['class' => 'control-label col-md-5']) !!}--}}
                    <div class="control-label col-md-5"><b class="text-info">Total Due Today</b></div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('today_total', old('today_total'),['class' => 'form-control validate[required, custom[number]]','id'=>'today_total', 'placeholder'=>'Total Due Today','readonly'=>true]) !!}
                        </div>
                    </div>
                </div>
            </div><!-- .col-md-6 -->
            <div class="col-md-6">
                <div class="form-group">
                    {{--                    {!! htmlspecialchars_decode(Form::label('credit_card_detail', 'Credit Card Detail <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                    <div class="control-label col-md-4"><b class="text-info">Credit Card Detail<span
                                    class="required">*</span></b></div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <select name="credit_card_detail" class="form-control select2 validate[required]"
                                    onchange="creditcard_detail(this.value)">
                                <option value=" ">Select Credit Card Detail</option>
                                <option value="">Add New Credit Card Detail</option>
                                @foreach($credit_card as $credit_card)
                                    <option value="{{$credit_card->id." ".$credit_card->credit_card_id}}">{{$credit_card->credit_card_type." ".$credit_card->credit_card_number}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="creditcard_div" hidden>
                    <div class="col-md-12">
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('credit_card_type', 'Payment Method <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Payment Method<span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::select('credit_card_type', array_add($card_type, '','Select Payment Method'), old('credit_card_type'), ['class' => 'form-control select2 validate[required]','id'=>'credit_card_type']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('credit_card_number', 'Card Number <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Card Number<span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('credit_card_number', old('credit_card_number'), ['class' => 'form-control validate[required,condRequired[creditCard]]', 'placeholder'=>'Credit Card Number','id'=>'credit_card_number']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('expire_card', 'Expire <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Expire <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('expire_card', old('expire_card'), ['id' => 'expire_card', 'class' => 'datepicker form-control validate[required]', 'placeholder'=>'Expire Card','id'=>'expire_card']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('cvv', 'CVV <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">CVV <span class="required">*</span></b>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('cvv', old('cvv'), ['class' => 'form-control validate[required, custom[number],condRequired[maxSize[3]]]', 'placeholder'=>'CVV','id'=>'cvv']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('first_name', 'First Name <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">First Name <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'First Name','id'=>'first_name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('last_name', 'Last Name <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Last Name <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Last Name','last_name','id'=>'last_name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary" onclick="addcreditcard()">Save Card</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{--                    {!! htmlspecialchars_decode(Form::label('lbl_billing_address', 'Billing Address <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                    <div class="control-label col-md-4"><b class="text-info">Billing Address <span
                                    class="required">*</span></b></div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <select name="address" class="form-control select2 validate[required]"
                                    onchange="billing_detail(this.value)">
                                <option value=" ">Select Address</option>
                                <option value="">Add New Billing Address</option>
                                @foreach($addresses as $address)
                                    <option value="{{$address->address_id}}">{{$address->address_1." ".$address->address_2}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="billing_div" hidden>
                    <div class="col-md-12">
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('address_line_1', 'Address Line 1 <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Address Line 1 <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('address_line_1', old('address_line_1'), ['id'=>'address_line_1','class' => 'form-control validate[required]', 'placeholder'=>'Address Line 1']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! Form::label('address_line_2', 'Line 2 ', ['class' => 'control-label col-md-4']) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Address Line 2 <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('address_line_2', old('address_line_2'), ['id'=>'address_line_2','class' => 'form-control', 'placeholder'=>'Line 2']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('city', 'City <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">City <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('city', old('city'), ['id' => 'city', 'class' => 'form-control validate[required]', 'placeholder'=>'City']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('state', 'State/Province <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">State/Province <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('state', old('state'), ['id'=>'state','class' => 'form-control validate[required]', 'placeholder'=>'State/Province']) !!}                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('postal_code', 'Postal Code <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Postal Code <span class="required">*</span></b>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('postal_code', old('postal_code'), ['id'=>'postal_code','class' => 'form-control validate[required]', 'placeholder'=>'Postal Code']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{--                            {!! htmlspecialchars_decode(Form::label('country', 'Country <span class="required">*</span>', ['class' => 'control-label col-md-4'])) !!}--}}
                            <div class="control-label col-md-4"><b class="text-info">Country <span
                                            class="required">*</span></b></div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('country', old('country'), ['id'=>'country','class' => 'form-control validate[required]', 'placeholder'=>'Country']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-3">
                                <button class="btn btn-primary" onclick="addaddress()">Save Address</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .col-md-6 -->
            <div class="form-group">
                <div class="col-md-9 col-md-offset-9">
                    <a href="{{ URL::route('reviewshipment') }}" class="btn btn-primary">Previous</a>
                    {!! Form::submit('  Submit  ', ['class'=>'btn btn-primary']) !!}
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
        function creditcard_detail(value) {
            if (value == "") {
                $('#creditcard_div').show();
            }
            else {
                $('#creditcard_div').hide();
            }
        }
        function addcreditcard() {
            credit_card_number = $("#credit_card_number").val();
            credit_card_type = $("#credit_card_type").val();
            expire_card = $("#expire_card").val();
            cvv = $("#cvv").val();
            first_name = $("#first_name").val();
            last_name = $("#last_name").val();
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/payment', // This is the url we gave in the route
                data: {
                    'credit_card_number': credit_card_number,
                    'credit_card_type': credit_card_type,
                    'expire_card': expire_card,
                    'cvv': cvv,
                    'first_name': first_name,
                    'last_name': last_name

                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    console.log(response);
                    if(response=='1') {
                        swal(" Your credit card information successfully store on paypal vault Successfully");
                        location.reload();
                    }
                    else
                    {
                        swal(" Some problem to store your credit card information on paypal vault");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function billing_detail(value) {
            if (value == "") {
                $('#billing_div').show();
            }
            else {
                $('#billing_div').hide();
            }
        }
        function addaddress() {
            address_line_1 = $("#address_line_1").val();
            address_line_2 = $("#address_line_2").val();
            city = $("#city").val();
            state = $("#state").val();
            postal_code = $("#postal_code").val();
            country = $("#country").val();
            flag = true;
            if (address_line_1 == '' || city == '' || state == '' || postal_code == '' || country == '') {
                flag = false;
            }
            if (flag == true) {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('input[name="_token"]').val()
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/payment/addaddress', // This is the url we gave in the route
                    data: {
                        'address_line_1': address_line_1,
                        'address_line_2': address_line_2,
                        'city': city,
                        'state': state,
                        'postal_code': postal_code,
                        'country': country

                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        console.log(response);
                        swal("Billing Address added Successfully");
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
        function gettotal() {
            fba_total = parseFloat($("#consulting").val(), 2) + parseFloat($("#label_charge").val(), 2) + parseFloat($("#prep_forward").val(), 2) + parseFloat($("#listing_service").val(), 2);
            total_cost = parseFloat($("#pre_ship_inspect").val(), 2) + parseFloat($("#shipping_cost").val(), 2) + parseFloat($("#port_fees").val(), 2) + parseFloat($("#custom_brokerage").val(), 2) + parseFloat($("#custom_duty").val(), 2) + parseFloat($("#inbound_shipping").val(), 2) + parseFloat(fba_total, 2);
            $("#total_fbaforward").val(fba_total.toFixed(2));
            $("#total_cost").val(total_cost.toFixed(2));
            $("#today_total").val(total_cost.toFixed(2));
        }
        $(document).ready(function () {
            fba_total = parseFloat($("#consulting").val(), 2) + parseFloat($("#label_charge").val(), 2) + parseFloat($("#prep_forward").val(), 2) + parseFloat($("#listing_service").val(), 2);
            total_cost = parseFloat($("#pre_ship_inspect").val(), 2) + parseFloat($("#shipping_cost").val(), 2) + parseFloat($("#port_fees").val(), 2) + parseFloat($("#custom_brokerage").val(), 2) + parseFloat($("#custom_duty").val(), 2) + parseFloat($("#inbound_shipping").val(), 2) + parseFloat(fba_total, 2);
            $("#total_fbaforward").val(fba_total.toFixed(2));
            $("#total_cost").val(total_cost.toFixed(2));
            $("#today_total").val(total_cost.toFixed(2));

        });
        $(document).ready(function () {
            $('.datepicker').datepicker({
                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months"
            });
        });

    </script>
@endsection