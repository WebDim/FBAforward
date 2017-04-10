<?php

namespace App\Http\Controllers;

use App\Order_shipment_quantity;
use Illuminate\Http\Request;

use App\Order;
use App\Shipments;
use App\Shipment_detail;
use App\Charges;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = "FBA Inventory";
        $user = \Auth::user();
        $user_role = $user->role_id;
        // \DB::enableQueryLog();

        $orders = Order::selectRaw('orders.order_id, sum(shipment_details.total) as total, orders.created_at')
            ->join('shipments', 'shipments.order_id', '=', 'orders.order_id', 'left')
            ->join('shipment_details', 'shipment_details.shipment_id', '=', 'shipments.shipment_id', 'left')
            ->where('orders.is_activated', '>=', '12')
            //->where('orders.is_activated', '<', '17')
            ->where('shipments.is_activated','>=','6')
            ->where('shipments.status','!=','1')
            //->where('shipments.is_activated','<','11')
           // ->Orwhere('shipments.status','0')
            ->where('orders.user_id', $user->id)
            ->orderBy('orders.created_at', 'desc')
            ->groupby('orders.order_id')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated', '>=', '12')
            //->where('orders.is_activated', '<', '17')
            ->where('shipments.is_activated','>=','6')
            ->where('shipments.status','!=','1')
            //->where('shipments.is_activated','<','11')
            //->Orwhere('shipments.status','0')
            ->where('orders.user_id', $user->id)
           ->get();

         //dd(\DB::getQueryLog());

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('customer.fba_inventory')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $count=$request->input('count');
        for ($cnt=1;$cnt<$count;$cnt++)
        {
            $sub_count=$request->input('sub_count'.$cnt);
            for ($sub_cnt=1;$sub_cnt<$sub_count;$sub_cnt++)
            {
                $data = array('order_id' => $request->input('order'),
                              'shipment_id' => $request->input('shipment_id'.$cnt),
                              'shipment_detail_id' => $request->input('shipment_detail_id'.$cnt.'_'.$sub_cnt),
                              'quantity' => $request->input('quantity'.$cnt.'_'.$sub_cnt)
                );
                Order_shipment_quantity::create($data);
            }
        }


        return redirect('customer')->with('success', 'Shipment Quantity Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function shipquantity(Request $request)
    {
        if($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::selectRaw('shipments.*')
                ->where('shipments.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total, shipment_details.shipment_detail_id, sum(order_shipment_quantities.quantity) as quantity, order_shipment_quantities.status')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->join('order_shipment_quantities','order_shipment_quantities.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->groupby('shipment_details.shipment_detail_id')
                ->get();
            return view('customer.ship_quantity')->with(compact('shipment','shipment_detail','order_id'));
        }
    }
}
