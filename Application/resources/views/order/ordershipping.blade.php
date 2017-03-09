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
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive no-padding">
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
                                        @elseif($user_role==9)
                                            <a onclick="opennote({{$order->order_id}})" class="btn btn-info">Add Notes</a>
                                        @elseif($user_role==10)
                                            @if($order->is_activated==11)
                                                <a href="{{ url('order/warehousecheckinform/'.$order->order_id)}}" class="btn btn-info">Warehouse Check In</a>
                                            @elseif($order->is_activated==13)
                                                <a onclick="viewchecklist('{{$order->order_id}}','Check List')">View Check List</a>
                                                <a onclick="order_status('{{$order->order_id}}','14')" class="btn btn-info">Submit</a>
                                            @elseif($order->is_activated==15)
                                                <a onclick="shippinglabel('{{$order->order_id}}')">Print Shipping Labels</a>
                                                <a onclick="order_status('{{$order->order_id}}','16')" class="btn btn-info">Submit</a>
                                               {{-- <a onclick="verifylabel({{$order->order_id}})" class="btn btn-info">Verify Label Complete</a>
                                                <a onclick="order_status('{{$order->order_id}}','16')" class="btn btn-info">Verify Shipment Load On Truck</a>
                                                --}}
                                            @endif
                                        @elseif($user_role==8)
                                            @if($order->is_activated==12)
                                                <a onclick="openreview({{$order->order_id}})">Review Warehouse Check In</a>
                                                <a onclick="opennote({{$order->order_id}})" class="btn btn-info">Add Notes</a>
                                                @if($order->shipmentplan==0)
                                                <a href="{{ url('order/createshipments/'.$order->order_id)}}" class="btn btn-info">create shipment</a>
                                                @elseif($order->shipmentplan==1)
                                                <a onclick="order_status('{{$order->order_id}}','13')" class="btn btn-info">Review Complete</a>
                                                @endif
                                            @elseif($order->is_activated==16)
                                                <a onclick="shipmentreview('{{$order->order_id}}')">Review Shipment</a>
                                               @if($order->verify_status==0)
                                                <a onclick="verifystatus('{{$order->order_id}}')" class="btn btn-info">Verify Changes</a>
                                                @endif
                                                <a onclick="order_status('{{$order->order_id}}','17')" class="btn btn-info">Complete</a>
                                            @endif

                                        @elseif($user_role==11)
                                            @if($order->is_activated==14)
                                             <a onclick="viewchecklist('{{$order->order_id}}','Review Order')">Review Order Requirement</a><br>
                                             <a onclick="reviewwork('{{$order->order_id}}')">Review Work Completed List</a><br>
                                             <a onclick="order_status('{{$order->order_id}}','15')" class="btn btn-info">Approve Work Completed</a>
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
                                        {!! htmlspecialchars_decode(Form::label('report', 'Upload Report<span class="required">*</span> ',['class' => 'control-label col-md-5'])) !!}
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
    <div class="modal fade" id="opennote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notes</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="main">
                            <div class="table-responsive no-padding">
                                <table id="note_list" class="table">

                                </table>
                            </div>
                            {!! Form::open(['url' =>  'order/addnotes', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group" id="shipping_div">
                                        {!! Form::hidden('orderid',old('orderid'), ['id'=>'orderid']) !!}
                                        {!! Form::label('shipping_note', 'Shipping Notes ',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                {!! Form::textarea('shipping_note', old('shipping_note'), ['class' => 'form-control','rows' => 2, 'cols' => 40]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="prep_div">
                                        {!! Form::label('prep_note', 'Prep Notes ',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                {!! Form::textarea('prep_note', old('prep_note'), ['class' => 'form-control','rows' => 2, 'cols' => 40]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('', '',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                {!! Form::submit('  Submit  ', ['class'=>'btn btn-primary',  'id'=>'add']) !!}
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
    <div class="modal fade" id="openreview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Review Warehouse Check In</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="review">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="checklistview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myLabel"></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="checklist">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="review_work" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Review Work Completed List</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="worklist">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipment_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Shipment Review</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="shipment">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipment_label" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Shipping Label</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="label">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="barcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Barcode</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="barcode_div">

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
            jQuery.noConflict();
            $("#order_id").val(order_id);
         $("#openformmodal").modal('show');

        }
        function opennote(order_id)
        {
            jQuery.noConflict();
            $("#orderid").val(order_id);

            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewnotes', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    response = $.parseJSON(response);
                    var trHTML = '';
                    trHTML+='<thead><tr><th>Shiping Notes</th><th>Prep Notes</th><th>Action</th></tr></thead><tbody>';
                    $.each(response, function (i, item) {
                        trHTML += '<tr><td><input type="hidden" name="id'+i+'" id="id'+i+'" value="'+item.id+'"><input type="text" name="shipping_note'+i+'" id="shipping_note'+i+'" value="'+item.shipping_notes+'" hidden>' + item.shipping_notes + '</td><td><input type="text" name="prep_note'+i+'" id="prep_note'+i+'" value="'+item.prep_notes+'" hidden>' + item.prep_notes + '</td><td><i class="fa fa-floppy-o" id="save'+i+'" style="display: none" onclick="savenote('+i+')"></i>&nbsp; <i class="fa fa-pencil" id="edit'+i+'" onclick="editnote('+i+')"></i>&nbsp; <i class="fa fa-trash" onclick="deletenote('+i+')"></i></td></tr>';
                    });
                    trHTML+="</tbody>";
                    $('#note_list').html(trHTML);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
            $("#opennote").modal('show');

        }
        function openbill(order_id)
        {
            jQuery.noConflict();
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
        function deletenote(no)
        {
            note_id=$("#id"+no).val();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/deletenote', // This is the url we gave in the route
                data: {
                    'note_id': note_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    //console.log(response);
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function editnote(no)
        {
            note_id=$("#id"+no).val();
            $("#shipping_note"+no).show();
            $("#prep_note"+no).show();
            $("#save"+no).show();
            $("#edit"+no).hide();
        }
        function savenote(no)
        {
            note_id=$("#id"+no).val();
            shipping_note=$("#shipping_note"+no).val();
            prep_note=$("#prep_note"+no).val();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/savenote', // This is the url we gave in the route
                data: {
                    'note_id': note_id,
                    'shipping_note':shipping_note,
                    'prep_note' : prep_note
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    //console.log(response);
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function openreview(order_id)
        {
            jQuery.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/warehousecheckinreview', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#review').html(response);
                    $("#openreview").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function order_status(order_id, status)
        {
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/orderstatus', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                    'status': status
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function viewchecklist(order_id,title)
        {
            jQuery.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewchecklist', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#checklist').html(response);
                    $('#myLabel').text(title);
                    $("#checklistview").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function reviewwork(order_id)
        {
            jQuery.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/reviewwork', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#worklist').html(response);
                    $("#review_work").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function verifyshipment(shipment_id,status)
        {

            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/verifylabel', // This is the url we gave in the route
                data: {
                    'shipment_id': shipment_id,
                    'status':status
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed

                    if (response == '2') {
                        $("#label" + shipment_id).hide();
                        $("#ship_load" + shipment_id).show();
                    }
                    else if (response == '3')
                    {
                        $("#label_tr").hide();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function shipmentreview(order_id)
        {
            jQuery.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/shipmentreview', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#shipment').html(response);
                    $("#shipment_review").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function verifystatus(order_id)
        {
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/verifystatus', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function shippinglabel(order_id)
        {
            jQuery.noConflict();
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/shippinglabel', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#label').html(response);
                    $("#shipment_label").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function getlabel(fnsku)
        {
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/getlabel', // This is the url we gave in the route
                data: {
                    'fnsku': fnsku,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#barcode_div').html(response);
                    $("#barcode").modal('show');
                    var prtContent = document.getElementById("barcode_div");
                    var WinPrint = window.open('', '', 'left=0,top=0,width=500,height=200,toolbar=0,scrollbars=0,status=0');
                    WinPrint.document.write(prtContent.innerHTML);
                    WinPrint.document.close();
                    WinPrint.focus();
                    WinPrint.print();
                    WinPrint.close();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function getotherlabel()
        {
            $.ajax({
                headers: {
                    'X-CSRF-Token':  "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/getotherlabel', // This is the url we gave in the route
                success: function (response) { // What to do if we succeed
                    $('#barcode_div').html(response);
                    $("#barcode").modal('show');
                    $("#barcode").modal.print();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection