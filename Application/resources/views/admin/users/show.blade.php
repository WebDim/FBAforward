@extends('layouts.admin.app')

@section('title', 'Profile')

@section('css')

@endsection

@section('content')
        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Profile
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ url('admin/users') }}"><i class="fa fa-users"></i> Users</a></li>
        <li class="active"><i class="fa fa-user"></i> Profile</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{ $user->name. ' Profile' }}</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <img src="{{ asset($user->avatar) }}" class="img-responsive img-circle" alt="{{ $user->name }}">
                    </div>
                    <div class="col-md-9">
                        <div class="table-responsive no-padding">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td style="width: 15%;"><span class="text-muted">Name:</span></td>
                                    <td><b class="text-info">{{ $user->name }}</b></td>
                                </tr>
                                <tr>
                                    <td><span class="text-muted">Email:</span></td>
                                    <td><b class="text-info">{{ $user->email }}</b></td>
                                </tr>
                                <tr>
                                    <td><span class="text-muted">Role:</span></td>
                                    <td><b class="text-info">{{ $user->role->name }}</b></td>
                                </tr>
                                <tr>
                                    <td><span class="text-muted">Job Title:</span></td>
                                    <td><b class="text-info">{{ isset($user->job_title) && !empty($user->job_title) ? $user->job_title : '-' }}</b></td>
                                </tr>
                                <tr>
                                    <td><span class="text-muted">Mobile:</span></td>
                                    <td><b class="text-info">{{ isset($user->mobile) && !empty($user->mobile) ? $user->mobile : '-' }}</b></td>
                                </tr>
                                <tr>
                                    <td><span class="text-muted">Address:</span></td>
                                    <td><b class="text-info">{{ $user->address }}</b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
        </div><!-- /.box-footer-->
    </div><!-- /.box -->
</section><!-- /.content -->
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
