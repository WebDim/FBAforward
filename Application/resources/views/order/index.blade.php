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
@endsection
@section('content')
<!-- Main content -->
<section class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-head-line col-md-10">{{$title}}</h3>
                <div class="col-md-2">
                    <a href="{{ url('order/shipment') }}" class="btn btn-primary">Create New Order</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive no-padding">
                    @if(!$orders->isEmpty())
                    <table id="data_table" class="table">
                        <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/0' }}">
                                            <b class="text-info">{{ $order->order_no }}</b>
                                        </a>
                                    </td>
                                    <td>
                                        <b class="text-info">{{ $orderStatus[$order->is_activated] }}</b>
                                    </td>
                                    <td>
                                        <b class="text-info">{{ $order->created_at }}</b>
                                    </td>
                                    <td>
                                        @if($order->is_activated == 0)
                                            <a href="{{ url('order/updateshipment/'.$order->order_id) }}" class="btn btn-info">Edit</a>
                                            <a href="#" onclick="remove_order({{$order->order_id}})" class="btn btn-danger">Delete</a>
                                        @elseif($order->is_activated == 2)
                                            <a href="{{ url('order/downloadreport/'.$order->order_id) }}">Download Report</a>
                                            <a onclick="approvereport({{$order->order_id}})" class="btn btn-info">Approve Inspection Report</a>
                                        @elseif($order->is_activated==4)
                                            {{--<a onclick="openquote({{$order->order_id}})">View Shipping Quote</a>--}}
                                            <a href="{{ url('order/downloadquote/'.$order->order_id) }}">Download Quote</a>

                                            <a onclick="approveshippingquote({{$order->order_id}})" class="btn btn-info">Approve Shipping Quote</a>
                                        @endif

                                        {{--@if($order->is_activated==1)
                                                <a href="#" onclick="order_status({{$order->order_id}},3)" class="btn btn-info">Approve</a>
                                                <a href="#" onclick="order_status({{$order->order_id}},4)" class="btn btn-danger">Reject</a>
                                        @endif--}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table id="data_table" class="table">
                        <thead>
                            <tr>
                                <th colspan="4">No Order Data Found !!</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                @endif
                </div>
            </div>
        </div>
</section>
<!-- /.content -->
<div class="modal fade" id="openquote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">View Shipping Quote</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="main">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <link href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#data_table').DataTable({});
    });
    function remove_order(order_id){
        if(confirm('Are you sure you want to delete this order!')){
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/removeorder', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    if(response == 0){
                        alert('Sorry! Somthing went wrong please delete leter');
                    }else {
                        $('#tr_'+order_id).remove();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    }
    function order_status(order_id,status){
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/orderstatus', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                    'status':status
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    alert('Order status successfully changed');


                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

    }
    function approvereport(order_id)
    {
        $.ajax({
            headers: {
                'X-CSRF-Token':  "{{ csrf_token() }}"
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/order/approvereport', // This is the url we gave in the route
            data: {
                'order_id': order_id,
            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                console.log(response);
                alert("Report Approved");
                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
    function openquote(order_id)
    {
        $.noConflict();
        $.ajax({
            headers: {
                'X-CSRF-Token':  "{{ csrf_token() }}"
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/order/viewshippingquote', // This is the url we gave in the route
            data: {
                'order_id': order_id,
            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
             $('#main').html(response);
                $("#openquote").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
    function approveshippingquote(order_id)
    {
        $.ajax({
            headers: {
                'X-CSRF-Token':  "{{ csrf_token() }}"
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/order/approveshippingquote', // This is the url we gave in the route
            data: {
                'order_id': order_id,
            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                console.log(response);
                //alert("Report Approved");
                if(response==1) {
                    location.reload();
                }
                //location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>
@endsection