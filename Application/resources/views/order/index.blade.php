@extends('layouts.frontend.app')
@section('title', 'Order Management')
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
                <h3 class="page-head-line col-md-10">Order Management</h3>
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
                                        <b class="text-info">{{ $order->order_no }}</b>
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
</script>
@endsection