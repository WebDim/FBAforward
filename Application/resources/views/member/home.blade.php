@extends('layouts.frontend.app')

@section('title', 'Member')

@section('css')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">FBAFORWARD - Dashboard</h2>
        </div>
    </div>
    <h4>Welcome To FBAFORWARD!</h4>
    <div class="row">
    @if($user->role->name=='Customer')
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $total_order }}</h3>
                    <p>Total Orders</p>
                </div>
                <div class="icon">
                    <i class="ion-android-cart"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $total_in_order }}</h3>
                    <p>Total Inprogress Orders</p>
                </div>
                <div class="icon">
                    <i class="ion-android-cart"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>  {{ $total_place_order }}</h3>
                    <p>Total Placed Orders</p>
                </div>
                <div class="icon">
                    <i class="ion-android-cart"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-gray">
                <div class="inner">
                    <h3>  {{ $total_inspect_order  }} </h3>
                    <p>Total Inspection Reports Approval</p>
                </div>
                <div class="icon">
                    <i class="ion-document-text"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3> {{ $total_shipping_order  }} </h3>
                    <p>Total Shipping Quote Approval</p>
                </div>
                <div class="icon">
                    <i class="ion-ios-paper"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-fuchsia">
                <div class="inner">
                    <h3> {{ $total_inventory  }} </h3>
                    <p>Total Inventory</p>
                </div>
                <div class="icon">
                    <i class="ion-ios-list"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3> @if(isset($total_payment))
                            $ {{ $total_payment[0]->payment_count  }}
                        @endif</h3>
                    <p>Total Payments</p>
                </div>
                <div class="icon">
                    <i class="ion-social-usd-outline"></i>
                </div>
                {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div><!-- ./col -->

    @elseif($user->role->name=='customer service' || $user->role->name=='Sales')
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $total_customer }}</h3>
                        <p>Total Customers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{$total_order}}</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-cart"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $total_in_order }}</h3>
                        <p>Total Inprogress Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-cart"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>  {{ $total_place_order }}</h3>
                        <p>Total Placed Orders</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-cart"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-gray">
                    <div class="inner">
                        <h3>  {{ $total_inspect_order  }} </h3>
                        <p>Total Inspection Reports Approval</p>
                    </div>
                    <div class="icon">
                        <i class="ion-document-text"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3> {{ $total_shipping_order  }} </h3>
                        <p>Total Shipping Quote Approval</p>
                    </div>
                    <div class="icon">
                        <i class="ion-ios-paper"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
    @elseif($user->role->name=='Accounting')
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3> {{ $total_invoice }} </h3>
                        <p>Total Invoice</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-cart"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>  @if(isset($total_amount))
                                $ {{ $total_amount[0]->amount_count  }}
                            @endif </h3>
                        <p>Total Amount</p>
                    </div>
                    <div class="icon">
                        <i class="ion-social-usd-outline"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Inspector')

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3> {{ $order_count }} </h3>
                        <p>Total Inpection Report</p>
                    </div>
                    <div class="icon">
                        <i class="ion-document-text"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Shipper')
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $total_customer }}</h3>
                        <p>Customer Lookup</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $shipping_quote_count }}</h3>
                        <p>Total Shipping Quote</p>
                    </div>
                    <div class="icon">
                        <i class="ion-ios-paper"></i>
                    </div>
                    <a href="{{ url('order/shippingquote') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $bill_lading_count }}</h3>
                        <p>Total Bill Of Lading</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-list"></i>
                    </div>
                    <a href="{{ url('order/billoflading') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $pre_alert_count }}</h3>
                        <p>Total Shipment Pre-Alert</p>
                    </div>
                    <div class="icon">
                        <i class="ion-ios-list"></i>
                    </div>
                    <a href="{{ url('order/prealert') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Logistics')

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $bill_lading_count }}</h3>
                        <p>Total Bill Of Lading</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-list"></i>
                    </div>
                    <a href="{{ url('order/billofladingapprove') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $clearance_count }}</h3>
                        <p>Total Custome Clearance</p>
                    </div>
                    <div class="icon">
                        <i class="ion-ios-paper"></i>
                    </div>
                    <a href="{{ url('order/customclearance') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $booking_count }}</h3>
                        <p>Total Delivery Booking</p>
                    </div>
                    <div class="icon">
                        <i class="ion-ios-list"></i>
                    </div>
                    <a href="{{ url('order/deliverybooking') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Warehouse Manager')
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3> {{ $review_count }} </h3>
                        <p>Total Review Order</p>
                    </div>
                    <div class="icon">
                        <i class="ion-document-text"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Warehouse Admin')
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $checkin_review_count }}</h3>
                        <p>Total Warehouse Check In Review</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-checkbox-outline"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $shipment_review_count }}</h3>
                        <p>Total Shipment Review</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-boat"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
        @elseif($user->role->name=='Warehouse Lead')

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $checkin_count }}</h3>
                        <p>Total Warehouse Check In</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-checkbox-outline"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $labor_count }}</h3>
                        <p>Total Order Labor</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-clipboard"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->

            <div class="col-lg-4 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $shipment_count }}</h3>
                        <p>Total complete Shipment</p>
                    </div>
                    <div class="icon">
                        <i class="ion-android-boat"></i>
                    </div>
                    {{--<a href="http://localhost:8000/admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                </div>
            </div><!-- ./col -->
    @endif
        </div>
@endsection
