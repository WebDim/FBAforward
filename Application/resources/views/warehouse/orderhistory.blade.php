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
                                    <a href="{{ url('order/details/'.$order->order_id) }}">
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
                                    <a href="javascript:void(0)" class="btn btn-info" onclick="viewchecklist('{{$order->order_id}}','Check List')">Print Label</a>
                                    &nbsp;
                                    @if($order->is_activated > 15)
                                    <a href="javascript:void(0)" class="btn btn-info" onclick="shippinglabel('{{$order->order_id}}')">Print Shipping Labels</a>
                                    @endif
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
        function viewchecklist(order_id, title) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/warehouse/viewchecklist', // This is the url we gave in the route
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
        function shippinglabel(order_id) {

            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/warehouse/shippinglabel', // This is the url we gave in the route
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
                url: '/warehouse/getlabel', // This is the url we gave in the route
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
                url: '/warehouse/getotherlabel', // This is the url we gave in the route
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

    </script>
@endsection