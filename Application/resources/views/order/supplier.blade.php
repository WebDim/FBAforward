@extends('layouts.frontend.app')

@section('title', 'Supplier Information')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Supplier Information</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'order/supplierdetail', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <td><span>Product</span></td>
                        <td><span>Total Unit</span></td>
                        <td><span>Suppliers</span></td>
                    </tr>
                    </thead>
                    <tbody>
    {{--*/ $cnt = 1 /*--}}

        @foreach($product as $products)

       <tr>
            <td><input type="hidden" name="shipment_detail_id{{ $cnt }}" value="{{ $products->shipment_detail_id }}">
                <input type="hidden" name="supplier_detail_id{{ $cnt }}" value="{{ $products->supplier_detail_id }}">
                <input type="hidden" name="product_id{{ $cnt }}" value="{{ $products->product_id }}">
                <b class="text-info">{{ $products->product_name }}</b></td>
            <td><input type="hidden" name="total{{ $cnt }}" value="{{ $products->total }}"><b class="text-info">{{ $products->total }}</b></td>
            <td><b class="text-info">
                    <select name="supplier{{ $cnt }}" class="form-control select2 validate[required]" onchange="add_Supplier(this.value)">
                        <option value="">Suppliers</option>
                        <option value="0">Add New</option>
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
    {!! Form::label('company_name', 'Company Name*', ['class' => 'control-label col-md-2']) !!}
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon"></span>
            {!! Form::text('company_name', old('company_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Company Name']) !!}
        </div>
    </div>
    {!! Form::label('contact_name', 'Contact Name*', ['class' => 'control-label col-md-2']) !!}
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon"></span>
            {!! Form::text('contact_name', old('contact_name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Contact Name']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('email', 'Email Address*', ['class' => 'control-label col-md-2']) !!}
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon"></span>
            {!! Form::text('email', old('email'), ['class' => 'form-control validate[required]', 'placeholder'=>'Email Address']) !!}
        </div>
    </div>
    {!! Form::label('phone', 'Phone #*', ['class' => 'control-label col-md-2']) !!}
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon"></span>
            {!! Form::text('phone', old('phone'), ['class' => 'form-control validate[required]', 'placeholder'=>'Phone #']) !!}
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::button( 'Add Supplier', ['class'=>'btn btn-primary', 'id'=>'add', 'onclick'=>'add_supplier()']) !!}
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
<script>
function add_Supplier(value) {
if(value=='0')
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
function add_supplier() {
company_name=$('#company_name').val();
contact_name=$('#contact_name').val();
email=$('#email').val();
phone=$('#phone').val();
$.ajax({
headers:
    {
        'X-CSRF-Token': $('input[name="_token"]').val()
    },
method: 'POST', // Type of response and matches what we said in the route
url: '/order/addsupplier', // This is the url we gave in the route
data: {'company_name' : company_name,
        'contact_name' : contact_name,
        'email' : email,
        'phone' : phone

}, // a JSON object to send back
success: function(response){ // What to do if we succeed
    console.log(response);
    alert("Supplier added Successfully");
    location.reload();
},
error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
    console.log(JSON.stringify(jqXHR));
    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
}
});

}
</script>
@endsection