<?php

namespace App\Http\Controllers\Admin;

use App\Addresses;
use App\Feature;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Package;
use App\Page;
use App\Product_labels;
use App\Supplier;
use Yajra\Datatables\Datatables;
use App\User;
use App\Setting;
use App\Role;
use App\Shipping_method;
use App\Outbound_method;
use App\Prep_service;
use App\Listing_service;

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
            /*->editColumn('package_id', function ($user) {
                if (!is_null($user->package)) {
                    return $user->package->name;
                } else {
                    return '-';
                }
            })*/
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
    public function getShippingmethod()
    {
       $methods = Shipping_method::all();
       return Datatables::of($methods)
            ->editColumn('shipping_name', '{{ $shipping_name }}')
            ->editColumn('price', function ($method) {
                        return $method->price;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/shippingmethod/' . $method->shipping_method_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/shippingmethod/' . $method->shipping_method_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getSuppliers()
    {
        $methods = Supplier::all();
        return Datatables::of($methods)
            ->editColumn('company_name', '{{ $company_name }}')
            ->editColumn('contact_name', function ($method) {
                return $method->contact_name;
            })
            ->editColumn('email', function ($method) {
                return $method->email;
            })
            ->editColumn('phone_number', function ($method) {
                return $method->phone_number;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/suppliers/' . $method->supplier_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/suppliers/' . $method->supplier_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getPrepservices()
    {
        $methods = Prep_service::all();
        return Datatables::of($methods)
            ->editColumn('service_name', '{{ $service_name }}')
            ->editColumn('price', function ($method) {
                return $method->price;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/prepservices/' . $method->prep_service_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/prepservices/' . $method->prep_service_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getOutboundmethod(Outbound_method $outbound_method){
        $methods = $outbound_method::all();
        return Datatables::of($methods)
            ->editColumn('outbound_name', function($method){
                return $method->outbound_name;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/outboundmethod/' . $method->outbound_method_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/outboundmethod/' . $method->outbound_method_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getProductlabel(Product_labels $product_label){
        $methods = $product_label::all();
        return Datatables::of($methods)
            ->editColumn('label_name', '{{ $label_name }}')
            ->editColumn('price', function ($method) {
                return $method->Price;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/productlabel/' . $method->product_label_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/productlabel/' . $method->product_label_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getListingservices()
    {
        $methods = Listing_service::all();
        return Datatables::of($methods)
            ->editColumn('service_name', '{{ $service_name }}')
            ->editColumn('price', function ($method) {
                return $method->price;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/listingservices/' . $method->listing_service_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/listingservices/' . $method->listing_service_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
    public function getAddresses()
    {
        $methods = Addresses::all();
        return Datatables::of($methods)
            ->editColumn('type', '{{ $type }}')
            ->editColumn('address_1', function ($method) {
                return $method->address_1;
            })
            ->editColumn('address_2', function ($method) {
                return $method->address_2;
            })
            ->editColumn('city', function ($method) {
                return $method->city;
            })
            ->editColumn('state', function ($method) {
                return $method->state;
            })
            ->editColumn('postal_code', function ($method) {
                return $method->postal_code;
            })
            ->editColumn('country', function ($method) {
                return $method->country;
            })
            ->addColumn('actions', function ($method) {
                if (\Auth::user()->role->name == 'Admin') {
                    $editBtn = '<a style="margin-right: 0.1em;" href="' . url('admin/addresses/' . $method->address_id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                    $deleteBtn = '&nbsp;<a href="' . url('admin/addresses/' . $method->address_id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                }
                $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })->make(true);
    }
}
				