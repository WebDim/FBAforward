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
                                        @if($order->is_activated == 3 && $order->shipmentplan==0)
                                            <a href="#" onclick="order_shipping({{$order->order_id}},{{$order->user_id}})" class="btn btn-info">Create Shipment</a>
                                        @else
                                            Shipments Already Created
                                        @endif
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

    </script>
@endsection