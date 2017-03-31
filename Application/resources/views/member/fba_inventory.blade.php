@extends('layouts.frontend.app')

@section('title', $title)

@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
    {!! Html::style('assets/dist/css/datatable/dataTables.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/responsive.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/dataTablesCustom.css') !!}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">{{ $title }}</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th>Order No</th>
                        <th>Total Quantity</th>
                        <th>Amazon Shipment Quantity</th>
                        <th>Pending Shipment Quantity</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--*/$total=0/*--}}
                    @foreach($orders as $order)
                        <tr>
                            <td> <a href="{{ url('order/details/'.$order->order_id) }}">
                                    <b class="text-info">ORD_{{ $order->order_id }}</b>
                                </a>
                            </td>
                            <td><b class="text-info">{{ $order->total }}</b></td>
                            <td><b class="text-info">@if($order->shipmentplan==1){{ $order->total }}@else {{ $total }}@endif</b></td>
                            <td><b class="text-info">@if($order->shipmentplan==0){{ $order->total }}@else {{ $total }}@endif</b></td>
                            <td><b class="text-info">{{ $order->created_at }}</b></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
            $('#list').DataTable({});
        });
    </script>
@endsection
