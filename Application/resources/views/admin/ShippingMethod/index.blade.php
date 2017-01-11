@extends('layouts.admin.app')

@section('title', 'Shipping Method')

@section('css')
    <!-- DataTables -->
    {!! Html::style('assets/dist/css/datatable/dataTables.bootstrap.min.css') !!}

    {!! Html::style('assets/dist/css/datatable/responsive.bootstrap.min.css') !!}

    {!! Html::style('assets/dist/css/datatable/dataTablesCustom.css') !!}
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-star"></i> Shipping Method
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active"><i class="fa fa-star"></i> Shipping Method</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Shipping Method List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <table id="data_table" class="table datatable dt-responsive" style="width:100%;">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <p class="text-muted small">
                    <i class="fa fa-pencil"></i> Edit Shipping Method |
                    <i class="fa fa-remove"></i> Delete Shipping Method
                </p>
            </div><!-- /.box-footer-->
        </div><!-- /.box -->
    </section><!-- /.content -->

    @include('layouts.admin.includes.message_boxes', ['item' => 'Shipping_method', 'delete' => true])

@endsection

@section('js')
    <!-- DataTables -->
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}

    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}

    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}

    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $("#data_table").DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! url("admin/datatables/shippingmethod") !!}',
                columns: [
                    {data: 'shipping_name', name: 'shipping_name'},
                    {data: 'price', name: 'price'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ]
            });
            table.column('2:visible').order('asc').draw();
        });
    </script>
@endsection
