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
                <div class="col-md-2">
                    <a href="{{ url('/shipment') }}" class="btn btn-primary">Create New Order</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4 style="text-align: center;text-transform: uppercase">In Progress Orders</h4>
                <hr>
                <div class="table-responsive no-padding">
                    <table id="inprogress_data" class="table" >
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
                                @if($order->is_activated==0)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/0' }}">
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
                                            <a href="{{ url('/shipment/'.$order->order_id) .'/edit' }}" class="btn btn-info">Edit</a>
                                            <a href="javascript:void(0)" onclick="remove_order({{$order->order_id}})" class="btn btn-danger">Delete</a>

                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <h4 style="text-align: center;text-transform: uppercase">Order Placed</h4>
                <hr>
                <div class="table-responsive no-padding">
                    <table id="place_data" class="table">
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
                            @if($order->is_activated==1)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/0' }}">
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

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <h4 style="text-align: center;text-transform: uppercase">Inspection Report</h4>
                <hr>
                <div class="table-responsive no-padding">
                    <table id="inspection_data" class="table">
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
                            @if($order->is_activated==2)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/0' }}">
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
                                            <a href="{{ url('order/downloadreport/'.$order->order_id) }}">Download Report</a>
                                            <a href="javascript:void(0)" onclick="approvereport({{$order->order_id}})" class="btn btn-info">Approve Inspection Report</a>

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <h4 style="text-align: center;text-transform: uppercase">Shipping Quote</h4>
                <hr>
                <div class="table-responsive no-padding">
                    <table id="shipping_data" class="table">
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
                            @if($order->is_activated==4)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/0' }}">
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
                                           {{--<a onclick="openquote({{$order->order_id}})">View Shipping Quote</a>--}}
                                            <a href="{{ url('order/downloadquote/'.$order->order_id) }}">Download Quote</a>
                                            <a href="javascript:void(0)" onclick="approveshippingquote({{$order->order_id}})" class="btn btn-info">Approve Shipping Quote</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
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
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
    $(document).ready(function() {
        $('#inprogress_data').DataTable({
            "pageLength": 5,
            "order": [[ 2, "desc" ]]
        });
        $('#place_data').DataTable({
            "pageLength": 5,
            "order": [[ 2, "desc" ]]
        });
        $('#inspection_data').DataTable({
            "pageLength": 5,
            "order": [[ 2, "desc" ]]
        });
        $('#shipping_data').DataTable({
            "pageLength": 5,
            "order": [[ 2, "desc" ]]
        });
    });
    function remove_order(order_id){
        if(confirm('Are you sure you want to delete this order!')){
            $('.preloader').css("display", "block");
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
                    $('.preloader').css("display", "none");
                    console.log(response);
                    if(response == 0){
                        swal('Sorry! Somthing went wrong please delete leter');
                    }else {
                        $('#tr_'+order_id).remove();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    }
    function order_status(order_id,status){
            $('.preloader').css("display", "block");
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
                    $('.preloader').css("display", "none");
                    console.log(response);
                    swal('Order status successfully changed');


                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

    }
    function approvereport(order_id)
    {
        $('.preloader').css("display", "block");
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
                $('.preloader').css("display", "none");
                console.log(response);
                swal("Report Approved");
                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                $('.preloader').css("display", "none");
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
    function openquote(order_id)
    {
        $('.preloader').css("display", "block");
        jQuery.noConflict();
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
                $('.preloader').css("display", "none");
                $('#main').html(response);
                $("#openquote").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                $('.preloader').css("display", "none");
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
    function approveshippingquote(order_id)
    {
        $('.preloader').css("display", "block");
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
                $('.preloader').css("display", "none");
                console.log(response);
                //swal("Report Approved");
                if(response==1) {
                    location.reload();
                }
                //location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                $('.preloader').css("display", "none");
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>
@endsection