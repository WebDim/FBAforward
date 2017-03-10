<?php

namespace App\Http\Controllers\Admin;

use App\Feature;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Package;
use App\Page;
use App\User;
use App\Order;
use App\Payment_detail;
use App\Amazon_inventory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    /**
     * DashboardController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->count();
        /*$packages = Package::all()->count();
        $features = Feature::all()->count();
        $pages = Page::page()->count();
        $posts = Page::post()->count();
        $subscriptions = \DB::table('subscriptions')->count();*/
        $total_order = Order::all()->count();
        $total_payment = Payment_detail::selectRaw('sum(total_cost) as payment_count')
            ->join('orders','orders.order_id','=','payment_details.order_id')
            ->get();
        $total_in_order= Order::where('is_activated','0')->count();
        $total_place_order= Order::where('is_activated','1')->count();
        return view('admin.dashboard')->with(compact('users', 'total_order','total_payment','total_in_order','total_place_order'));
    }

}
