<?php

namespace App\Http\Controllers\Admin;

use App\Feature;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Package;
use App\Page;
use Yajra\Datatables\Datatables;
use App\User;
use App\Setting;
use App\Role;

class DatatablesController extends Controller
{

    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getUsers()
    {
        $users = User::all();

        return Datatables::of($users)
            ->editColumn('name', '<a href="{{ url(\'admin/users/\'.$id) }}"><b>{{ $name }}</b></a>')
            ->editColumn('role_id', function ($user) {
                if (!is_null($user->role)) {
                    return $user->role->name;
                } else {
                    return '-';
                }
            })
            ->editColumn('package_id', function ($user) {
                if (!is_null($user->package)) {
                    return $user->package->name;
                } else {
                    return '-';
                }
            })
            ->addColumn('avatar', function ($user) {
                return '<a href="' . url('admin/users/' . $user->id) . '"><img src="' . asset($user->avatar) . '" style="height:50px;" class="img-circle" alt="User Avatar"></a>';
            })
            ->addColumn('actions', function ($user) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/users/' . $user->id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    if (!is_null($user->role) && $user->role->name != 'Admin') {
                        $deleteBtn = '&nbsp;<a href="' . url('admin/users/' . $user->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                    } else {
                        $deleteBtn = '';
                    }
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }

    public function getSettings()
    {
        $settings = Setting::all();

        return Datatables::of($settings)
            ->editColumn('value', function ($setting) {
                return htmlentities(strlen($setting->getOriginal('value')) > 50 ? substr($setting->getOriginal('value'), 0, 50) : $setting->getOriginal('value'));
            })
            ->addColumn('actions', function ($setting) {
                $editBtn = '<a style="margin-right: 0.2em;" href="' . url('admin/settings/' . $setting->id . '/edit/') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';

                return $editBtn;
            })->make(true);
    }

    public function getRoles()
    {
        $roles = Role::all();

        return Datatables::of($roles)
            ->editColumn('routes', function ($role) {
                return htmlentities(strlen($role->getOriginal('routes')) > 50 ? substr($role->getOriginal('routes'), 0, 50) : $role->getOriginal('routes'));
            })
            ->addColumn('actions', function ($role) {
                $editBtn = '<a style="margin-right: 0.2em;" href="' . url('admin/roles/' . $role->id . '/edit/') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                $deleteBtn = '';
                if ($role->name != 'Admin') {
                    $deleteBtn = '&nbsp;<a href="' . url('admin/roles/' . $role->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Permanent Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                return $editBtn . $deleteBtn;
            })->make(true);
    }

    public function getMenus()
    {
        $menus = Menu::all();

        return Datatables::of($menus)
            ->addColumn('actions', function ($menu) {
                $editBtn = '<a style="margin-right: 0.2em;" href="' . url('admin/menus/' . $menu->id . '/edit/') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                $deleteBtn = '&nbsp;<a href="' . url('admin/menus/' . $menu->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Permanent Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                return $editBtn . $deleteBtn;
            })->make(true);
    }

    public function getPackages()
    {
        $packages = Package::all();

        return Datatables::of($packages)
            ->editColumn('name', '<a href="{{ url(\'admin/packages/\'.$id) }}"><b>{{ $name }}</b></a>')
            ->editColumn('cost', function ($package) {
                return $package->cost . '/' . $package->cost_per;
            })
            ->addColumn('actions', function ($package) {
                $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/packages/' . $package->id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                $deleteBtn = '&nbsp;<a href="' . url('admin/packages/' . $package->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';

                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }

    public function getFeatures()
    {
        $features = Feature::all();

        return Datatables::of($features)
            //->editColumn('name', '<a href="{{ url(\'admin/features/\'.$id) }}"><b>{{ $name }}</b></a>')
            ->addColumn('actions', function ($feature) {
                $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/features/' . $feature->id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                $deleteBtn = '&nbsp;<a href="' . url('admin/features/' . $feature->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';

                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }

    public function getPages()
    {
        $pages = Page::all();

        return Datatables::of($pages)
            ->editColumn('title', '<a href="{{ url(\'admin/pages/\'.$id) }}" target="_blank"><b>{{ $title }}</b></a>')
            ->addColumn('actions', function ($page) {
                $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/pages/' . $page->id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';

                $deleteBtn = '&nbsp;<a href="' . url('admin/pages/' . $page->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';

                $viewBtn = '<a style="margin-right: 0.2em;" href="' . url($page->slug) . '"  title="View" target="blank"><i class="fa fa-2 fa-eye"></i></a>';

                $buttons = '' . $editBtn . $viewBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
}
				