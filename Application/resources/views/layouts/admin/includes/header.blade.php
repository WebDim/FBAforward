<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{{ getSetting('SHORT_SITE_TITLE') }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{{ getSetting('SITE_TITLE') }}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle"  data-toggle="dropdown">
						<span class="hidden-xs">Welcome {{ Auth::user()->name }} !</span>
						<span class="caret"></span>
                        <img src="{{ asset(Auth::user()->avatar) }}" class="user-image" alt="User Image"/>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{ asset(Auth::user()->avatar) }}" class="img-circle" alt="User Image">
                            <p>
                                {{ Auth::user()->name }}
                                <small>{{ Auth::user()->job_title }}</small>
                                <small>{{ 'Role: ' . Auth::user()->role->name }}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ url('admin/users/'.Auth::user()->id) }}" class="btn btn-default btn-flat"><i class="fa fa-btn fa-user"></i> Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" data-box="#mb-signout" class="btn btn-danger btn-flat mb-control"><i class="fa fa-btn fa-sign-out"></i> Log out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- =============================================== -->
