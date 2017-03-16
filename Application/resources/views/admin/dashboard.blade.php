@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('css')
{!! Html::style('assets/dist/css/ionicons.min.css') !!}
@endsection


@section('content')
        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-dashboard"></i> Dashboard
    </h1>
    <ol class="breadcrumb">
        <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
    </ol>

</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Admin Dashboard</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{ $users }}</h3>
                                    <p>Users</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                                <a href="{{ url('admin/users') }}" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->
                       <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>{{ $total_order }}</h3>
                                    <p>Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion-android-cart"></i>
                                </div>
                                <a href="{{ url('admin/orders') }}" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>{{ $total_in_order }}</h3>
                                    <p>Inprogress Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion-android-cart"></i>
                                </div>
                                <a href="{{ url('admin/orders') }}" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{ $total_place_order }}</h3>
                                    <p>Placed Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="ion-android-cart"></i>
                                </div>
                                <a href="{{ url('admin/orders') }}" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-6 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-gray">
                                <div class="inner">
                                    <h3> @if(isset($total_payment))
                                            $ {{ $total_payment[0]->payment_count  }}
                                        @endif</h3>
                                    <p>Payments</p>
                                </div>
                                <div class="icon">
                                    <i class="ion-social-usd-outline"></i>
                                </div>
                                <a href="javascript:void(0)" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-fuchsia">
                                <div class="inner">
                                    <h3>{{ $customers }}</h3>
                                    <p>Customers</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                                <a href="{{ url('admin/customers') }}" class="small-box-footer">More info <i
                                            class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div><!-- ./col -->

                    </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer">
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col-md-12 -->
    </div> <!-- /.row -->
</section><!-- /.content -->
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
