<!-- start navigation -->
<style>
    .circle {
        display: inline-block;
        height: 10px;
        width: 10px;
        -moz-border-radius: 10px;
        border-radius: 5px;
        background-color: #363;
    }
    #notification_modal {
        left: 25%;
        right: auto;
        width: 624px;
        height: 700px;
    }
</style>
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
                    {{--*/$user = \Auth::user();/*--}}
                    @if('Customer' === \Auth::user()->role->name)
                                <li><a href="javascript:void(0)" onclick="getnotification('0')"><i class="fa fa-bell"></i>@if($user->notification()->unread()->count()>0)<span class="circle" id="count"></span>@else<span id="count"></span>@endif</a> </li>
                                {{--*/ $old_user= session('old_user')/*--}}
                                {{--*/$new_user=session('new_user')/*--}}
                                {{--*/session()->forget('new_user');/*--}}
                            @if(session('old_user'))
                            <li><a href="/member/switchuser">Switch Back</a> </li>
                            @endif
                            {{--@if(!isset($old_user))
                            <li><a href="{{ url('/creditcard_detail') }}">Paypal Vault</a></li>
                            @endif--}}
                    @else
                            {{--*/ $role= \App\Role::find($user->role_id) /*--}}
                        <li><a  href="javascript:void(0)" onclick="getnotification('1')"><i class="fa fa-bell"></i>@if($role->notification()->unread()->count()>0)<span class="circle" id="count"></span>@else<span id="count"></span>@endif</a> </li>
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
                                    <a class="external" href="{{ '/invoice' }}"><i class="fa fa-btn fa-user"></i> Invoice Report</a>
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
                                    <a class="external" href="{{ url('warehouse/warehousecheckin') }}"><i class="fa fa-btn fa-user"></i> Warehouse Check In Order</a>
                                    <a class="external" href="{{url('warehouse/orderlabor')}}"><i class="fa fa-btn fa-user"></i>Order Labor</a>
                                    <a class="external" href="{{url('warehouse/completeshipment')}}"><i class="fa fa-btn fa-user"></i>Complete Shipment</a>
                                </li>
                            @endif
                            @if('Warehouse Admin'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('warehouse/adminreview') }}"><i class="fa fa-btn fa-user"></i> Warehouse Check In Review</a>
                                    <a class="external" href="{{ url('warehouse/adminshipmentreview') }}"><i class="fa fa-btn fa-user"></i> Shipment Review</a>
                                </li>
                            @endif
                            @if('Warehouse Manager'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('warehouse/managerreview') }}"><i class="fa fa-btn fa-user"></i> Manager Review</a>
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
<div class="modal fade" id="notification_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>--}}
                <h4 class="modal-title" id="myModalLabel">Notifications</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="noti_div">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="checkread()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function getnotification(role)
    {

        $.ajax({
            headers: {
                'X-CSRF-Token':  "{{ csrf_token() }}"
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/member/getnotification', // This is the url we gave in the route
            data: {
                role : role,
            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                console.log(response);
                response = $.parseJSON(response);
                var trHTML = '';
                trHTML+='<thead><tr><th></th></tr></thead><tbody>';

                $.each(response.notification, function (i, item) {

                     trHTML += '<tr><td id="id' + item.id + '"><input type="hidden" id="role" value="'+response.role+'">' + item.subject + '</td></tr>';
                });

                trHTML+="</tbody>";

                $('#noti_div').html(trHTML);
                $("#notification_modal").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
    function checkread() {
        role=$("#role").val();
       if(role==null)
       {
           $("#notification_modal").modal("hide");
           $("#count").hide();
       }
       else {
           $.ajax({
               headers: {
                   'X-CSRF-Token': "{{ csrf_token() }}"
               },
               method: 'POST', // Type of response and matches what we said in the route
               url: '/member/checkread', // This is the url we gave in the route
               data: {
                   role: role,
               }, // a JSON object to send back
               success: function (response) { // What to do if we succeed
                   $("#notification_modal").modal("hide");
                   $("#count").hide();
               },
               error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                   console.log(JSON.stringify(jqXHR));
                   console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
               }
           });
       }
    }
</script>