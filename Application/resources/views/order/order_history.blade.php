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
                    @if(!$orders->isEmpty())
                        <table id="data_table" class="table">
                            <thead>
                            <tr>
                                <th>Order No</th>
                                <th>Status</th>
                                <th>Created At</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr id="tr_{{$order->order_id}}">
                                    <td>
                                        <a href="{{ url('order/details/'.$order->order_id).'/1' }}">
                                            <b class="text-info">{{ $order->order_no }}</b>
                                        </a>
                                    </td>
                                    <td>
                                        <b class="text-info">{{ $orderStatus[$order->is_activated] }}</b>
                                    </td>
                                    <td>
                                        <b class="text-info">{{ $order->created_at }}</b>
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
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#data_table').DataTable({});
        });

    </script>
@endsection