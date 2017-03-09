<!-- start navigation -->
<nav class="navbar navbar-default navbar-fixed-top laraship-nav" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
            </button>
            <a href="{{ url('/') }}" class="navbar-brand external">{{ getSetting('SITE_TITLE') }}</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right text-uppercase">
                <li><a href="{{ url('/#home') }}">Home</a></li>
                @if (!Auth::guest())
            @if('Customer' === \Auth::user()->role->name)
                {{--*/ $old_user= session('old_user')/*--}}
                @if(session('old_user'))
                    <li><a href="/order/switchuser/{{$old_user}}/1">Switch user</a> </li>
                    @endif
                    {{--@if(!isset($old_user))--}}
                    {{--<li><a href="{{ url('/creditcard_detail') }}">Paypal Vault</a></li>--}}
                    {{--@endif--}}
                @endif
                @endif
               <!-- <li><a href="{{ url('/#feature') }}">Features</a></li>
                <li><a class="{{ Auth::guest() ? '':'external' }}"
                       href="{{ Auth::guest() ? url('/#pricing') : url('member/pricing') }}"><b>Pricing</b></a></li> -->
                <li><a href="{{ url('/#contact') }}">Contact Us</a></li>
              {{--  @foreach(getMenuItems('HEADER') as $item)
                    <li><a class="external" href="{{ url($item->url) }}"><b>{{ $item->title }}</b></a></li>
                @endforeach --}}
				@if (Auth::guest())
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <b>My Account</b>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
						<!--
                        @if($pages->count())
                            @foreach($pages as $page)
                                <li><a class="external" href="{{ url($page->slug) }}">{{ $page->title }}</a></li>
                            @endforeach
                        @endif
						-->
                        <li><a class="external" href="{{ url('/login') }}">Login</a></li>
                        <li><a class="external" href="{{ url('/register') }}">Register</a></li>  
                    </ul>
                </li>
				@endif
                @if (!Auth::guest())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <b>Welcome {{ Auth::user()->name }} !</b>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            @if(!isset($old_user))
                            <li><a class="external" href="{{ url('member/home') }}"><i class="fa fa-btn fa-home"></i>&nbsp;Dashboard</a>
                            </li>
                            <li><a class="external" href="{{ url('member/profile') }}"><i class="fa fa-btn fa-user"></i>&nbsp;Profile</a>
                            </li>
                            @endif
                            @if('Customer' === \Auth::user()->role->name)
                            <li>
                                <a class="external" href="{{ url('order/index') }}"><i class="fa fa-btn fa-user"></i> Order Management</a>
                            </li>
                                @if(!isset($old_user))
                                {{--<li><a class="external" href="{{ url('order/orderhistory') }}"><i class="fa fa-btn fa-user"></i> Order History</a>--}}
                                {{--</li>--}}
                                <li><a class="external" href="{{ url('/amazon_credential') }}"><i class="fa fa-cog"></i>&nbsp;Amazon Credential</a>
                                </li>
                                <li><a class="external" href="{{ url('member/amazoninventorylist') }}"><i class="fa fa-list"></i>&nbsp;Amazon Inventory List</a>
                                </li>
                                @endif
                            @endif
                            @if('Inspector'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/inspectionreport') }}"><i class="fa fa-btn fa-user"></i> Inspection Report</a>
                                </li>
                            @endif
                            @if('Accounting'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/getinvoice_detail') }}"><i class="fa fa-btn fa-user"></i> Invoice Report</a>
                                </li>
                            @endif
                            @if('Shipper'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/customers') }}"><i class="fa fa-btn fa-user"></i>Customers</a>
                                    <a class="external" href="{{ url('order/shippingquote') }}"><i class="fa fa-btn fa-user"></i> Shipping Quote</a>
                                    <a class="external" href="{{ url('order/billoflading') }}"><i class="fa fa-btn fa-user"></i> Bill Of Lading</a>
                                    <a class="external" href="{{ url('order/prealert') }}"><i class="fa fa-btn fa-user"></i> Shipment Pre Alert</a>
                                </li>
                            @endif
                            @if('Warehouse Lead'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/warehousecheckin') }}"><i class="fa fa-btn fa-user"></i> Warehouse Check In Order</a>
                                    <a class="external" href="{{url('order/orderlabor')}}"><i class="fa fa-btn fa-user"></i>Order Labor</a>
                                    <a class="external" href="{{url('order/completeshipment')}}"><i class="fa fa-btn fa-user"></i>Complete Shipment</a>
                                </li>
                            @endif
                            @if('Warehouse Admin'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/adminreview') }}"><i class="fa fa-btn fa-user"></i> Warehouse Check In Review</a>
                                    <a class="external" href="{{ url('order/adminshipmentreview') }}"><i class="fa fa-btn fa-user"></i> Shipment Review</a>
                                </li>
                            @endif
                            @if('Warehouse Manager'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/managerreview') }}"><i class="fa fa-btn fa-user"></i> Manager Review</a>
                                </li>
                            @endif
                            @if('Logistics'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/billofladingapprove') }}"><i class="fa fa-btn fa-user"></i> Bill Of Lading</a>
                                    <a class="external" href="{{ url('order/customclearance') }}"><i class="fa fa-btn fa-user"></i> Custom Clearance</a>
                                    <a class="external" href="{{ url('order/deliverybooking') }}"><i class="fa fa-btn fa-user"></i> Delivery Booking</a>
                                </li>
                            @endif
                            @if('Sales'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/orderlist') }}"><i class="fa fa-btn fa-user"></i> Orders</a>
                                    <a class="external" href="{{ url('order/customers') }}"><i class="fa fa-btn fa-user"></i>Customers</a>
                                </li>
                            @endif
                            @if('customer service'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/orderlist') }}"><i class="fa fa-btn fa-user"></i> Orders</a>
                                    <a class="external" href="{{ url('order/customers') }}"><i class="fa fa-btn fa-user"></i>Customers</a>
                                 </li>
                            @endif
                            @if('Admin' === \Auth::user()->role->name)
                                <li><a class="external" href="{{ url('admin/dashboard') }}"><i
                                                class="fa fa-btn fa-user-secret"></i>&nbsp;
                                        Admin Panel</a>
                            @endif
                            <li><a class="external" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>&nbsp;Logout</a>
                            </li>
                        </ul>
                    </li>
                    <li class="hidden-xs"><img src="{{ asset(Auth::user()->avatar) }}"
                                               style="height: 32px;width: 32px;margin-top: 18px;"/></li>
                @endif
            </ul>
        </div>
    </div>
</nav>