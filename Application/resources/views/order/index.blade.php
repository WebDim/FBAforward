@extends('layouts.frontend.app')
@section('title', 'Order Management')
@section('content')
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Order Management</h3>
            <div class="box-tools pull-right">
                <a href="{{ url('order/shipment') }}" class="btn btn-primary">Create New Order</a>
            </div>
        </div>
        <div class="box-body">
            <table id="data_table" class="table datatable dt-responsive" style="width:100%;">
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
                                <a href="{{ url('order/updateshipment/'.$order->order_id) }}" class="btn btn-info">Edit</a>
                                <a href="#" onclick="remove_order({{$order->order_id}})" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</section>
<!-- /.content -->
@endsection
@section('js')
<script type="text/javascript">
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
