@extends('layouts.frontend.app')
@section('title', 'Order Payment')
@section('content')
    @include('layouts.frontend.tabs', ['data' => 'payment'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
        {!! Form::open(['url' => 'order/payment', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('pre_ship_inspect', 'Pre Shipment Inspection', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('pre_ship_inspect', old('pre_ship_inspect',$price['pre_shipment_inspection']), ['class' => 'form-control validate[required]', 'placeholder'=>'Pre Shipment Inspection','onblur'=>'gettotal()']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('shipping_cost', 'Shipping Cost', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('shipping_cost', old('shipping_cost',$price['shipping_cost']), ['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost','onblur'=>'gettotal()']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('port_fees', 'Port Fees', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('port_fees', old('port_fees',$price['port_fee']),['class' => 'form-control validate[required]', 'placeholder'=>'Port Fees','onblur'=>'gettotal()']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('custom_brokerage', 'Custom Brokerage', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_brokerage', old('custom_brokerage',$price['custom_brokerage']),['class' => 'form-control validate[required]', 'placeholder'=>'Custom Brokerage','onblur'=>'gettotal()']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('custom_duty', 'Custom Duty', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_duty', old('custom_duty',$price['custom_duty']),['class' => 'form-control validate[required]', 'placeholder'=>'Custom Duty','onblur'=>'gettotal()']) !!}
                        </div>
                    </div>
                </div>

                {!! Form::label('Fbaforward_Services', 'FBAFORWARD SERVICES:', ['class' => 'control-label']) !!}
                    <div class="form-group">
                        {!! Form::label('consulting', 'Consulting Charge', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('consulting', old('consulting',$price['consult_charge']),['class' => 'form-control validate[required]', 'placeholder'=>'Consulting Charge','onblur'=>'gettotal()']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('label_charge', 'Label Charge', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('label_charge', old('label_charge',$price['label_charge']),['class' => 'form-control validate[required]', 'placeholder'=>'Label Charge','onblur'=>'gettotal()']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('prep_forward', 'Prep Forwarding', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('prep_forward', old('prep_forward',$price['prep_forwarding']),['class' => 'form-control validate[required]', 'placeholder'=>'Prep Forwarding','onblur'=>'gettotal()']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('listing_service', 'Listing Services', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('listing_service', old('listing_service',$price['listing_service']),['class' => 'form-control validate[required]', 'placeholder'=>'Listing Services','onblur'=>'gettotal()']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('total_fbaforward', 'Total Fbaforward', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('total_fbaforward', old('total_fbaforward'),['class' => 'form-control', 'placeholder'=>'Total Fbaforward','readonly'=>true]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('inbound_shipping', 'Inbound Shipping', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('inbound_shipping', old('inbound_shipping',$price['inbound_shipping']),['class' => 'form-control', 'placeholder'=>'Inbound Shipping','onblur'=>'gettotal()']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('total_cost', 'Total Cost', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('total_cost', old('total_cost'),['class' => 'form-control', 'placeholder'=>'Total Cost','readonly'=>true]) !!}
                            </div>
                        </div>
                    </div>
            </div><!-- .col-md-6 -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('credit_card_detail', 'Credit Card Detail *', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-8">
                        <div class="input-group">
                            <select name="credit_card_detail" class="form-control select2 validate[required]" onchange="creditcard_detail(this.value)">
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
                        {!! Form::label('credit_card_type', 'Payment Method *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::select('credit_card_type', array_add($card_type, '','Select Payment Method'), old('credit_card_type'), ['class' => 'form-control select2 validate[required]']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('credit_card_number', 'Card Number *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::text('credit_card_number', old('credit_card_number'), ['class' => 'form-control validate[required]', 'placeholder'=>'Credit Card Number']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('expire_card', 'Expire *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::text('expire_card', old('expire_card'), ['id' => 'expire_card', 'class' => 'datepicker form-control validate[required]', 'placeholder'=>'Expire Card']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('cvv', 'CVV *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::text('cvv', old('cvv'), ['class' => 'form-control validate[required]', 'placeholder'=>'CVV']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('first_name', 'First Name *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::text('first_name', old('first_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'First Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('last_name', 'Last Name *', ['class' => 'control-label col-md-4']) !!}
                        <div class="col-md-8">
                            <div class="input-group">
                                {!! Form::text('last_name', old('last_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Last Name']) !!}
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
                    {!! Form::label('lbl_billing_address', 'Billing Address *', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-8">
                        <div class="input-group">
                            <select name="address" class="form-control select2 validate[required]" onchange="billing_detail(this.value)">
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
                            {!! Form::label('address_line_1', 'Address Line 1 *', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('address_line_1', old('address_line_1'), ['class' => 'form-control validate[required]', 'placeholder'=>'Address Line 1']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('address_line_2', 'Line 2 ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('address_line_2', old('address_line_2'), ['class' => 'form-control', 'placeholder'=>'Line 2']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('city', 'City *', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('city', old('city'), ['id' => 'city', 'class' => 'form-control validate[required]', 'placeholder'=>'City']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('state', 'State/Province *', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('state', old('state'), ['class' => 'form-control validate[required]', 'placeholder'=>'State/Province']) !!}                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('postal_code', 'Postal Code *', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('postal_code', old('postal_code'), ['class' => 'form-control validate[required]', 'placeholder'=>'Postal Code']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('country', 'Country *', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('country', old('country'), ['class' => 'form-control validate[required]', 'placeholder'=>'Country']) !!}
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
        function creditcard_detail(value)
        {
            if(value == "")
            {
                $('#creditcard_div').show();
            }
            else
            {
                $('#creditcard_div').hide();
            }
        }
        function addcreditcard() {
            credit_card_number=$("#credit_card_number").val();
            credit_card_type=$("#credit_card_type").val();
            expire_card=$("#expire_card").val();
            cvv=$("#cvv").val();
            first_name=$("#first_name").val();
            last_name=$("#last_name").val();
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/addcreditcard', // This is the url we gave in the route
                data: {
                    'credit_card_number' :credit_card_number,
                'credit_card_type' : credit_card_type,
                'expire_card' : expire_card,
                'cvv' : cvv,
                'first_name' : first_name,
                'last_name' : last_name

                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    alert(" Your credit card information successfully store on paypal vault Successfully");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function billing_detail(value)
        {
            if(value == "")
            {
                $('#billing_div').show();
            }
            else
            {
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
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/addaddress', // This is the url we gave in the route
                data: {
                    'address_line_1': address_line_1,
                    'address_line_2': address_line_2,
                    'city': city,
                    'state': state,
                    'postal_code': postal_code,
                    'country': country,

                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    alert("Billing Address added Successfully");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        }
        function gettotal() {
            fba_total=parseFloat($("#consulting").val(),2)+parseFloat($("#label_charge").val(),2)+parseFloat($("#prep_forward").val(),2)+parseFloat($("#listing_service").val(),2);
            total_cost=parseFloat($("#pre_ship_inspect").val(),2)+parseFloat($("#shipping_cost").val(),2)+parseFloat($("#port_fees").val(),2)+parseFloat($("#custom_brokerage").val(),2)+parseFloat($("#custom_duty").val(),2)+parseFloat($("#inbound_shipping").val(),2)+fba_total;
            $("#total_fbaforward").val(fba_total);
            $("#total_cost").val(total_cost);
        }
        $(document).ready(function () {
            fba_total=parseFloat($("#consulting").val(),2)+parseFloat($("#label_charge").val(),2)+parseFloat($("#prep_forward").val(),2)+parseFloat($("#listing_service").val(),2);
            total_cost=parseFloat($("#pre_ship_inspect").val(),2)+parseFloat($("#shipping_cost").val(),2)+parseFloat($("#port_fees").val(),2)+parseFloat($("#custom_brokerage").val(),2)+parseFloat($("#custom_duty").val(),2)+parseFloat($("#inbound_shipping").val(),2)+fba_total;
            $("#total_fbaforward").val(fba_total);
            $("#total_cost").val(total_cost);

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