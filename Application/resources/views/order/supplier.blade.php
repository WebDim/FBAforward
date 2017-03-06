@extends('layouts.frontend.app')

@section('title', 'Supplier Information')

@section('content')
    @include('layouts.frontend.tabs', ['data' => 'supplier'])
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/supplierdetail', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th class="col-md-6"><span>Product</span></th>
                        <th class="col-md-3"><span>Total Unit</span></th>
                        <th class="col-md-3"><span>Suppliers</span></th>
                    </tr>
                    </thead>
                    <tbody>
    {{--*/ $cnt = 1 /*--}}

        @foreach($product as $products)
        <input type="hidden" id="order_id", name="order_id" value="{{$products->order_id}}">
       <tr>
            <td class="col-md-6"><input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                <input type="hidden" name="supplier_detail_id{{ $cnt }}" value="{{ $products->supplier_detail_id }}">
                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                <b class="text-info">@if($products->product_nick_name==''){{ $products->product_name}} @else {{$products->product_nick_name}} @endif</b></td>
            <td class="col-md-3"><input type="hidden" name="total{{ $cnt }}" value="{{ $products->total }}"><b class="text-info">{{ $products->total }}</b></td>
            <td class="col-md-3"><b class="text-info">
                    <select name="supplier{{ $cnt }}" class="form-control select2 validate[required]" onchange="add_Supplier(this.value)">
                        <option value=" ">Select Suppliers</option>
                        <option value="">Add New</option>
                        @foreach ($supplier as $suppliers)
                            <option value="{{ $suppliers->supplier_id }}" @if($products->supplier_id==$suppliers->supplier_id) {{ "selected" }} @endif>  {{ $suppliers->company_name }}</option>
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
<div id="main" hidden>
<hr>
<h4>Add New Supplier</h4>
<div class="col-md-12">
<div class="form-group">
    {!! htmlspecialchars_decode(Form::label('company_name', 'Company Name<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
    <div class="col-md-4">
        <div class="">
            <span class=""></span>
            {!! Form::text('company_name', old('company_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Company Name']) !!}
        </div>
    </div>
    {!! htmlspecialchars_decode(Form::label('contact_name', 'Contact Name<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
    <div class="col-md-4">
        <div class="">
            <span class=""></span>
            {!! Form::text('contact_name', old('contact_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Contact Name']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! htmlspecialchars_decode(Form::label('email', 'Email Address<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
    <div class="col-md-4">
        <div class="">
            <span class=""></span>
            {!! Form::email('email', old('email'), ['class' => 'form-control validate[required]', 'placeholder'=>'Email Address']) !!}
        </div>
    </div>
    {!! htmlspecialchars_decode(Form::label('phone', 'Phone #<span class="required">*</span>', ['class' => 'control-label col-md-2'])) !!}
    <div class="col-md-4">
        <div class="">
            <span class=""></span>
            {!! Form::text('phone', old('phone'), ['class' => 'form-control validate[required]', 'placeholder'=>'Phone #']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::button( 'Add Supplier', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'addsupplier()']) !!}
</div>
</div>
</div>
<div class="col-md-12">
<div class="form-group">
     <div class="col-md-9 col-md-offset-9">
         <a href="{{ URL::route('shipment') }}" class="btn btn-primary">Previous</a>
        {!! Form::submit('  Next  ', ['class'=>'btn btn-primary']) !!}
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

function add_Supplier(value) {
if(value == "")
{
    $('#main').show();
}
else
{
    $('#main').hide();
}
}
</script>
<script>
function addsupplier() {
company_name=$('#company_name').val();
contact_name=$('#contact_name').val();
email=$('#email').val();
phone=$('#phone').val();
    $("#validate").validate({
        rules: {
            "company_name": {
                required: true,

            },
            "email": {
                required: true,
                email: true,
            },
            "contact_name": {
                required:true,
            },
            "phone": {
                required:true,
            },

        },
        messages:{
            "company_name": {
                    required:"Company name is required",
                },
            "email": {
                required:"Email address is required",
                email:"Enter valid email address",
            },
            "contact_name": {
                required:"Contact name is required",
            },
            "phone": {
                required:"Phone # is required",
            },
        }
    });
    if($('#validate').valid()) {
        $.ajax({
            headers: {
                'X-CSRF-Token': $('input[name="_token"]').val()
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/order/addsupplier', // This is the url we gave in the route
            data: {
                'company_name': company_name,
                'contact_name': contact_name,
                'email': email,
                'phone': phone

            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                console.log(response);
                alert("Supplier added Successfully");
                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }

}
</script>
@endsection