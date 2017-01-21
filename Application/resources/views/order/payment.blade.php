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
                    {!! Form::label('lbl_shipping_cost', 'Shipping Cost', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('shipping_cost', '', ['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('lbl_port_fees', 'Port Fees', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('port_fees', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('lbl_custom_brokerage', 'Custom Brokerage', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_brokerage', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('lbl_custom_duty', 'Custom Duty', ['class' => 'control-label col-md-5']) !!}
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('custom_duty', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                        </div>
                    </div>
                </div>

                {!! Form::label('Fbaforward_Services', 'FBAFORWARD SERVICES:', ['class' => 'control-label']) !!}
                    <div class="form-group">
                        {!! Form::label('lbl_consulting', 'Consulting Charge', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('consulting', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_label', 'Label Charge', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('label', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_prep_forward', 'Prep Forwarding', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('prep_forward', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_listing_service', 'Listing Services', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('listing_service', '',['class' => 'form-control validate[required]', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_total_fbaforward', 'Total Fbaforward', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('total_fbaforward', '',['class' => 'form-control', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_inbound_shipping', 'Inbound Shipping', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('inbound_shipping', '',['class' => 'form-control', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('lbl_total_cost', 'Total Cost', ['class' => 'control-label col-md-5']) !!}
                        <div class="col-md-7">
                            <div class="input-group">
                                {!! Form::text('total_cost', '',['class' => 'form-control', 'placeholder'=>'Shipping Cost']) !!}
                            </div>
                        </div>
                    </div>


            </div><!-- .col-md-6 -->
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('credit_card_type', 'Pyament Method *', ['class' => 'control-label col-md-4']) !!}
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
                            {!! Form::text('expire_card', old('expire_card'), ['id' => 'expire_card', 'class' => 'form-control validate[required]', 'placeholder'=>'Expire Card']) !!}
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
                <div class="form-group">
                    {!! Form::label('lbl_billing_address', 'Billing Address *', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-8">
                        <div class="input-group">
                            <select name="address" class="form-control select2 validate[required]" onchange="add_address(this.value)">
                                <option value="">Select Address</option>
                                <option value="0">Add New Billing Address</option>
                            </select>
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

    </script>
@endsection