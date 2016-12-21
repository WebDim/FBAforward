<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="image text-center">
                <a href="{{ url('/') }}"><img src="{{ asset(getSetting('SITE_LOGO')) }}" class="" alt="Logo"></a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="{{ Request::is('admin/dashboard') ? 'active': '' }}">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ Request::is('/') ? 'active': '' }}">
                <a href="{{ url('/') }}">
                    <i class="fa fa-desktop"></i> <span>View Site</span>
                </a>
            </li>
            <li class="treeview {{ Request::is('admin/user*') ? 'active': '' || Request::is('admin/role*') ? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Users</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/users')? 'active': '' }}">
                        <a href="{{ url('admin/users') }}">
                            <i class="fa fa-list"></i> <span>Manage Users</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/users/create')? 'active': '' }}">
                        <a href="{{ url('admin/users/create') }}">
                            <i class="fa fa-plus"></i> <span>Add User</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/role*')? 'active': '' }}">
                        <a href="#"><i class="fa fa-key"></i> Roles Settings <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::is('admin/roles')? 'active': '' }}"><a
                                        href="{{ url('admin/roles') }}"><i class="fa fa-list"></i> Manage Roles</a></li>
                            <li class="{{ Request::is('admin/roles/create')? 'active': '' }}"><a
                                        href="{{ url('admin/roles/create') }}"><i class="fa fa-plus"></i> Add Role</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="treeview {{ Request::is('admin/package*') || Request::is('admin/feature*') ? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-briefcase"></i> <span>Packages</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/packages')? 'active': '' }}">
                        <a href="{{ url('admin/packages') }}">
                            <i class="fa fa-list"></i> <span>Manage Packages</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/packages/create')? 'active': '' }}">
                        <a href="{{ url('admin/packages/create') }}">
                            <i class="fa fa-plus"></i> <span>Add Package</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/features')? 'active': '' }}">
                        <a href="{{ url('admin/features') }}">
                            <i class="fa fa-list"></i> <span>Manage Features</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/features/create')? 'active': '' }}">
                        <a href="{{ url('admin/features/create') }}">
                            <i class="fa fa-plus"></i> <span>Add Feature</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview {{ Request::is('admin/page*')? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-files-o"></i> <span>Content</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/pages')? 'active': '' }}">
                        <a href="{{ url('admin/pages') }}">
                            <i class="fa fa-list"></i> <span>Manage Content</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/pages/create')? 'active': '' }}">
                        <a href="{{ url('admin/pages/create') }}">
                            <i class="fa fa-plus"></i> <span>Add Content</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview {{ Request::is('admin/menu*')? 'active': '' }}">
                <a href="#"><i class="fa fa-list-alt"></i> Menus Settings <i
                            class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/menus')? 'active': '' }}"><a
                                href="{{ url('admin/menus') }}"><i class="fa fa-list"></i> Manage Menus</a></li>
                    <li class="{{ Request::is('admin/menus/create')? 'active': '' }}"><a
                                href="{{ url('admin/menus/create') }}"><i class="fa fa-plus"></i> Add Menu</a>
                    </li>
                </ul>
            </li>
            <li class="treeview {{ Request::is('admin/setting*')? 'active': '' }}">
                <a href="#">
                    <i class="fa fa-gears"></i> <span>Settings</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/settings')? 'active': '' }}">
                        <a href="{{ url('admin/settings') }}">
                            <i class="fa fa-list"></i> <span>Manage Settings</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/settings/create')? 'active': '' }}">
                        <a href="{{ url('admin/settings/create') }}">
                            <i class="fa fa-plus"></i> <span>Add Setting</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<!-- =============================================== -->