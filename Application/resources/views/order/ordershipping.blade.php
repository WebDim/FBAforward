@extends('layouts.frontend.app')
@section('title', 'Order Shipping')
@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-head-line col-md-10">Order Shipping</h3>
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
                                        <a href="{{ url('order/details/'.$order->order_id.'/0/'.$order->user_id) }}">
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
                                        @if($user_role==12)
                                            @if($order->is_activated == 1)
                                            <a onclick="openform({{$order->order_id}})">Upload Report</a>
                                            @endif
                                        @elseif($user_role==5)
                                            @if($order->is_activated==3 || $order->is_activated == 1)
                                            <a href="{{ url('order/shippingquoteform/'.$order->order_id)}}" class="btn btn-info">Shipping Quote</a>
                                            @endif
                                            @if($order->is_activated==6)
                                            <a href="{{ url('order/billofladingform/'.$order->order_id)}}" class="btn btn-info">Bill Of Lading</a>
                                            @endif
                                            @if($order->is_activated==8)
                                            <a href="{{ url('order/prealertform/'.$order->order_id)}}" class="btn btn-info">Shipment Pre-Alert</a>
                                            @endif
                                        @elseif($user_role==6)
                                            @if($order->is_activated==7)
                                            <a onclick="openbill({{$order->order_id}})">View Lading Bill</a>
                                            <a onclick="approvebilloflading({{$order->order_id}})" class="btn btn-info">Approve Lading Bill</a>
                                            @elseif($order->is_activated==9)
                                            <a href="{{ url('order/customclearanceform/'.$order->order_id)}}" class="btn btn-info">Customs Clearance</a>
                                            @elseif($order->is_activated==10)
                                                <a href="{{ url('order/deliverybookingform/'.$order->order_id)}}" class="btn btn-info">Delivery Booking</a>
                                            @endif
                                        @endif
                                        {{--@if($order->is_activated == 3 && $order->shipmentplan==0)
                                            <a href="#" onclick="order_shipping({{$order->order_id}},{{$order->user_id}})" class="btn btn-info">Create Shipment</a>

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
    <div class="modal fade" id="openformmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Upload Inspection Report</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open(['url' =>  'order/inspectionreport', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::hidden('order_id',old('order_id'), ['id'=>'order_id']) !!}
                                        {!! Form::label('report', 'Upload Report *',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                        <div class="input-group">
                                            {!! Form::file('report', old('report'), ['class' => 'validate[required]']) !!}
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('', '',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                        <div class="input-group">
                                            {!! Form::submit('  Next  ', ['class'=>'btn btn-primary',  'id'=>'add']) !!}
                                        </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="openbill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">View Lading Bill</h4>
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
            $('#data_table').DataTable({
                "searching":false,
            });
        });
        function order_shipping(order_id,user_id){

                $.ajax({
                    headers: {
                        'X-CSRF-Token':  "{{ csrf_token() }}"
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/createshipments', // This is the url we gave in the route
                    data: {
                        'order_id': order_id,
                        'user_id': user_id
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        console.log(response);
                        alert('Shipment created');


                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
        }
        function openform(order_id)
        {
            $.noConflict();
            $("#order_id").val(order_id);
         $("#openformmodal").modal('show');

        }
        function openbill(order_id)
        {
            $.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewbilloflading', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#main').html(response);
                    $("#openbill").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function approvebilloflading(order_id)
        {
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/approvebilloflading', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    //alert("Report Approved");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection