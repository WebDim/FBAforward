@extends('layouts.admin.app')

@section('title', 'Customers')

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
        <i class="fa fa-users"></i> Customers
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><i class="fa fa-users"></i> Customers</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Customers List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <table id="data_table" class="table datatable dt-responsive" style="width:100%;">
                <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                  {{--  <th>Package</th>--}}
                    <th>Role</th>
                    <th>Created At</th>

                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div><!-- /.box-body -->

    </div><!-- /.box -->
</section><!-- /.content -->

@include('layouts.admin.includes.message_boxes', ['item' => 'Customer', 'delete' => true])

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
            ajax: '{!! url("admin/datatables/customers") !!}',
            columns: [
                {data: 'avatar', name: 'avatar', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
        {{--{data: 'package_id', name: 'package_id'},--}}
                {data: 'role_id', name: 'role_id'},
                {data: 'created_at', name: 'created_at'},
            ]
        });
        table.column('4:visible').order('asc').draw();
    });
</script>
@endsection