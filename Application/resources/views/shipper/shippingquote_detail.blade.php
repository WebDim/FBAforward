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
                            <th>Action</th>
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
                                <td><a href="javascript:void(0)" onclick="viewquote('{{$order->order_id}}','{{$order->user_id}}')">View Shipping Quote</a></td>
                            </tr>
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
        $(document).ready(function () {
            $('#data_table').DataTable({
                "order": [[ 1, "asc" ]],
            });
        });
        function viewquote(order_id,user_id)
        {
            $('.preloader').css("display", "block");
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/shipper/viewquote', // This is the url we gave in the route
                data: {
                    'order_id': order_id,
                    'user_id' : user_id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    $('#main').html(response);
                    //$('#myModalLabel').text(title);
                    $("#openquote").modal('show');
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