@extends('layouts.frontend.app')

@section('title', $title)

@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
        .modal-dialog {
            width: 80%;
            height: 80%;
            margin: 3;
            padding: 0;
        }

        .modal-content {
            height: auto;
            min-height: 80%;
            border-radius: 0;
        }
    </style>
    {!! Html::style('assets/dist/css/datatable/dataTables.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/responsive.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/dataTablesCustom.css') !!}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">{{ $title }}</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th>Order No</th>
                        <th>Total Quantity</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/$total=0/*--}}
                    @foreach($orders as $order)
                        <tr>
                            <td> <a href="{{ url('order/details/'.$order->order_id) }}">
                                    <b class="text-info">ORD_{{ $order->order_id }}</b>
                                </a>
                            </td>
                            <td><b class="text-info">{{ $order->total }}</b></td>
                            <td><b class="text-info">{{ $order->created_at }}</b></td>
                            <td>
                                @foreach($shipments as $shipment)
                                    @if($shipment->order_id==$order->order_id )
                                         <a href="javascript:void(0)" onclick="shipquantity('{{$order->order_id}}','{{$shipment->shipment_id}}')" class="btn btn-info">ship Quantity For {{ $shipment->shipping_name }}</a>
                                    @endif
                                @endforeach
                            </td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
<div class="modal fade" id="shipmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
<h4 class="modal-title" id="myModalLabel">Quantity For Shipment</h4>
</div>
<div class="modal-body">
<div class="row">
<div class="col-md-12" id="ship_div">

</div>
</div>
</div>
</div>
</div>
</div>
@endsection
@section('js')

{!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
{!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
{!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
{!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}

<script type="text/javascript">

$(document).ready(function() {
$('#list').DataTable({});
});
function shipquantity(order_id, shipment_id) {

$('.preloader').css("display", "block");
$.ajax({
headers: {
'X-CSRF-Token': "{{ csrf_token() }}"
},
method: 'POST', // Type of response and matches what we said in the route
url: '/customer/shipquantity', // This is the url we gave in the route
data: {
'order_id': order_id,
'shipment_id' : shipment_id,
}, // a JSON object to send back
success: function (response) { // What to do if we succeed

$('.preloader').css("display", "none");
$('#ship_div').html(response);
$("#shipmodel").modal('show');
},
error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
$('.preloader').css("display", "none");
console.log(JSON.stringify(jqXHR));
console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
}
});
}
</script>
<script type="text/javascript">
var flag=true;
function checkquantity(cnt,sub_cnt,total) {
qty =parseInt($('#quantity'+cnt+'_'+sub_cnt).val());
if(qty > total)
{
swal('Quantity Must be Less Than Or Equal To Pending Shipped Quantity');
$('#quantity'+cnt+'_'+sub_cnt).val(" ");
flag = false;
}
else
{
flag=true;
}
}
function check() {

if (flag == false) {
return false;
}
else {
return true;
}
}

</script>
@endsection
