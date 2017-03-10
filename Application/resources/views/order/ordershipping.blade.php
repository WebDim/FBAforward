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
                                        <b class="text-info">ORD_{{ $order->order_id }}</b>
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
                                            <a href="javascript:void(0)" onclick="openform({{$order->order_id}})">Upload Report</a>
                                        @endif
                                    @elseif($user_role==5)
                                        @if($order->is_activated==3 || $order->is_activated == 1)
                                            <a href="{{ url('order/shippingquoteform/'.$order->order_id)}}"
                                               class="btn btn-info">Shipping Quote</a>
                                        @endif
                                        @if($order->is_activated==6)
                                            <a href="{{ url('order/billofladingform/'.$order->order_id)}}"
                                               class="btn btn-info">Bill Of Lading</a>
                                        @endif
                                        @if($order->is_activated==8)
                                            <a href="{{ url('order/prealertform/'.$order->order_id)}}"
                                               class="btn btn-info">Shipment Pre-Alert</a>
                                        @endif
                                    @elseif($user_role==6)
                                        @if($order->is_activated==7)
                                            <a href="javascript:void(0)" onclick="openbill({{$order->order_id}})">View Lading Bill</a>
                                            <a href="javascript:void(0)" onclick="approvebilloflading({{$order->order_id}})" class="btn btn-info">Approve
                                                Lading Bill</a>
                                        @elseif($order->is_activated==9)
                                            <a href="{{ url('order/customclearanceform/'.$order->order_id)}}"
                                               class="btn btn-info">Customs Clearance</a>
                                        @elseif($order->is_activated==10)
                                            <a href="{{ url('order/deliverybookingform/'.$order->order_id)}}"
                                               class="btn btn-info">Delivery Booking</a>
                                        @endif
                                    @elseif($user_role==9)
                                        <a href="javascript:void(0)" onclick="opennote({{$order->order_id}})" class="btn btn-info">Add Notes</a>
                                    @elseif($user_role==10)
                                        @if($order->is_activated==11)
                                            <a href="{{ url('order/warehousecheckinform/'.$order->order_id)}}"
                                               class="btn btn-info">Warehouse Check In</a>
                                        @elseif($order->is_activated==13)
                                            <a href="javascript:void(0)" onclick="viewchecklist('{{$order->order_id}}','Check List')">View Check
                                                List</a>
                                            <a href="javascript:void(0)" onclick="order_status('{{$order->order_id}}','14')" class="btn btn-info">Submit</a>
                                        @elseif($order->is_activated==15)
                                            <a href="javascript:void(0)" onclick="shippinglabel('{{$order->order_id}}')">Print Shipping Labels</a>
                                            @foreach($label_count as $label_counts)
                                                @if(($order->order_id==$label_counts->order_id) && ($order->shipment_count==$label_counts->shipment_count))
                                                <a href="javascript:void(0)" onclick="order_status('{{$order->order_id}}','16')" class="btn btn-info">Submit</a>
                                                @endif
                                            @endforeach
                                                {{-- <a onclick="verifylabel({{$order->order_id}})" class="btn btn-info">Verify Label Complete</a>
                                             <a onclick="order_status('{{$order->order_id}}','16')" class="btn btn-info">Verify Shipment Load On Truck</a>
                                             --}}
                                        @endif
                                    @elseif($user_role==8)
                                        @if($order->is_activated==12)
                                            <a href="javascript:void(0)" onclick="openreview({{$order->order_id}})">Review Warehouse Check In</a>
                                            <a href="javascript:void(0)" onclick="opennote({{$order->order_id}})" class="btn btn-info">Add
                                                Notes</a>
                                            @if($order->shipmentplan==0)
                                                <a href="{{ url('order/createshipments/'.$order->order_id)}}"
                                                   class="btn btn-info">create shipment</a>
                                            @elseif($order->shipmentplan==1)
                                                <a href="javascript:void(0)" onclick="order_status('{{$order->order_id}}','13')"
                                                   class="btn btn-info">Review Complete</a>
                                            @endif
                                        @elseif($order->is_activated==16)
                                            <a href="javascript:void(0)" onclick="shipmentreview('{{$order->order_id}}')">Review Shipment</a>
                                            @if($order->verify_status==0)
                                                <a href="javascript:void(0)" onclick="verifystatus('{{$order->order_id}}')" class="btn btn-info">Verify
                                                    Changes</a>
                                            @endif
                                            <a href="javascript:void(0)" onclick="order_status('{{$order->order_id}}','17')" class="btn btn-info">Complete</a>
                                        @endif

                                    @elseif($user_role==11)
                                        @if($order->is_activated==14)
                                            <a href="javascript:void(0)" onclick="viewchecklist('{{$order->order_id}}','Review Order')">Review
                                                Order Requirement</a><br>
                                            <a href="javascript:void(0)" onclick="reviewwork('{{$order->order_id}}')">Review Work Completed
                                                List</a><br>
                                            <a href="javascript:void(0)" onclick="order_status('{{$order->order_id}}','15')" class="btn btn-info">Approve
                                                Work Completed</a>
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
    <div class="modal fade" id="openformmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="openbill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="opennote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notes</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="main">
                            <div class="table-responsive no-padding">
                                <table id="note_list" class="table">

                                </table>
                            </div>
                            {!! Form::open(['url' =>  'order/addnotes', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate', 'onsubmit'=>'return checknote()']) !!}
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
    <div class="modal fade" id="openreview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="checklistview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="review_work" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="shipment_review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="shipment_label" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    <div class="modal fade" id="barcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
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
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
        $(document).ready(function () {
            $('#data_table').DataTable({
                "order": [[ 1, "asc" ]],

            });
        });
        function order_shipping(order_id, user_id) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/createshipments', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                    'user_id': user_id
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('.preloader').css("display", "none");
                    console.log(response);
                    alert('Shipment created');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function openform(order_id) {

            $("#order_id").val(order_id);
            $("#openformmodal").modal('show');

        }
        function opennote(order_id) {
            $("#orderid").val(order_id);
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewnotes', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    response = $.parseJSON(response);
                    var trHTML = '';
                    trHTML += '<thead><tr><th>Shiping Notes</th><th>Prep Notes</th><th>Action</th></tr></thead><tbody>';
                    $.each(response, function (i, item) {
                        trHTML += '<tr id="note'+i+'"><td><input type="hidden" name="id' + i + '" id="id' + i + '" value="' + item.id + '"><input type="text" name="shipping_note' + i + '" id="shipping_note' + i + '" value="' + item.shipping_notes + '" hidden><span id="shipping_span'+i+'">' + item.shipping_notes + '</span></td><td><input type="text" name="prep_note' + i + '" id="prep_note' + i + '" value="' + item.prep_notes + '" hidden><span id="prep_span'+i+'">' + item.prep_notes + '</span></td><td><a href="javascript:void(0)" class="fa fa-floppy-o" id="save' + i + '" style="display: none" onclick="savenote(' + i + ')"></a>&nbsp; <a href="javascript:void(0)" class="fa fa-pencil" id="edit' + i + '" onclick="editnote(' + i + ')"></a>&nbsp; <a href="javascript:void(0)" class="fa fa-trash" onclick="deletenote(' + i + ')"></a></td></tr>';
                    });
                    trHTML += "</tbody>";
                    $('#note_list').html(trHTML);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
            $("#opennote").modal('show');

        }
        function openbill(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewbilloflading', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#main').html(response);
                    $("#openbill").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function approvebilloflading(order_id) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/approvebilloflading', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    console.log(response);
                    alert("Bill of lading Approved");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function deletenote(no) {
            note_id = $("#id" + no).val();
            conf=confirm("Are you sure want to delete note");
            if(conf==true) {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/deletenote', // This is the url we gave in the route
                    data: {
                        'note_id': note_id,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        //console.log(response);
                        //location.reload();
                        $("#note" + no).hide();

                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        function editnote(no) {
            note_id = $("#id" + no).val();
            $("#shipping_note" + no).show();
            $("#prep_note" + no).show();
            $("#save" + no).show();
            $("#edit" + no).hide();
            $("#shipping_span"+no).hide();
            $("#prep_span"+no).hide();

        }
        function savenote(no) {
            note_id = $("#id" + no).val();
            shipping_note = $("#shipping_note" + no).val();
            prep_note = $("#prep_note" + no).val();
            if(shipping_note=='' && prep_note=='')
            {
                alert('Any one note compulsary');
            }
            else {
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/order/savenote', // This is the url we gave in the route
                    data: {
                        'note_id': note_id,
                        'shipping_note': shipping_note,
                        'prep_note': prep_note
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        opennote($('#orderid').val());
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
        function openreview(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/warehousecheckinreview', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#review').html(response);
                    $("#openreview").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function order_status(order_id, status) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/orderstatus', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                    'status': status
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function viewchecklist(order_id, title) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/viewchecklist', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#checklist').html(response);
                    $('#myLabel').text(title);
                    $("#checklistview").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function reviewwork(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/reviewwork', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#worklist').html(response);
                    $("#review_work").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function verifyshipment(shipment_id, status) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/verifylabel', // This is the url we gave in the route
                data: {
                    'shipment_id': shipment_id,
                    'status': status
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    if (response == '2') {
                        $("#label" + shipment_id).hide();
                        $("#ship_load" + shipment_id).show();
                    }
                    else if (response == '3') {
                        $("#label_tr").hide();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function shipmentreview(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/shipmentreview', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#shipment').html(response);
                    $("#shipment_review").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function verifystatus(order_id) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/verifystatus', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function shippinglabel(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/shippinglabel', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#label').html(response);
                    $("#shipment_label").modal('show');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function getlabel(fnsku) {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/getlabel', // This is the url we gave in the route
                data: {
                    'fnsku': fnsku,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
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
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function getotherlabel() {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/order/getotherlabel', // This is the url we gave in the route
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#barcode_div').html(response);
                    $("#barcode").modal('show');
                    $("#barcode").modal.print();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function checknote() {
            shipping_note=$("#shipping_note").val();
            prep_note=$("#prep_note").val();
            if(shipping_note=='' && prep_note=='')
            {
                alert("Any one note compulsary");
                return false;
            }
        }
    </script>
@endsection