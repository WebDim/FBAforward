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
                    <li><a href="{{ url('/creditcard_detail') }}">Paypal Vault</a></li>
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
                            <li><a class="external" href="{{ url('member/home') }}"><i class="fa fa-btn fa-home"></i>&nbsp;Dashboard</a>
                            </li>
                            <li><a class="external" href="{{ url('member/profile') }}"><i class="fa fa-btn fa-user"></i>&nbsp;Profile</a>
                            </li>
                            @if('Customer' === \Auth::user()->role->name)
                            <li>
                                <a class="external" href="{{ url('order/index') }}"><i class="fa fa-btn fa-user"></i> Order Management</a>
                            </li>
                            <li>
                                <a class="external" href="{{ url('order/orderhistory') }}"><i class="fa fa-btn fa-user"></i> Order History</a>
                            </li>
                            <li><a class="external" href="{{ url('/amazon_credential') }}"><i class="fa fa-cog"></i>&nbsp;Amazon Credential</a>
                            </li>
                            <li><a class="external" href="{{ url('member/amazoninventorylist') }}"><i class="fa fa-list"></i>&nbsp;Amazon Inventory List</a>
                            </li>
                            @endif
                            @if('Warehouse Manager'===\Auth::user()->role->name)
                                <li>
                                    <a class="external" href="{{ url('order/ordershipping') }}"><i class="fa fa-btn fa-user"></i> Ship Order</a>
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