<?php

namespace App\Http\Controllers;

use App\Amazon_inventory;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Shipping_method;
use App\Http\Requests\ShipmentRequest;
use App\Shipment_detail;
use phpDocumentor\Reflection\Types\Integer;

class OrderController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

    }
    public function shipment()
    {
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id',$user->id)->get();
        return view('order.shipment')->with(compact('shipping_method','product'));
    }
    public function addshipment(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count=$request->input('count');
        for($cnt=1; $cnt<=$count; $cnt++)
        {
            $product_id=explode(' ',$request->input('product_desc'.$cnt));
            $shipment = array('user_id'=>$user->id,
                                'product_id'=>$product_id[1],
                                'shipping_method_id'=>$request->input('shipping_method'.$cnt),
                                'split_shipment'=>$request->input('split_shipment'),
                                'goods_ready_date'=>date('Y-m-d H:i:s',strtotime($request->input('date'))),
                                'fnsku'=>$request->input('upc_fnsku'.$cnt),
                                'qty_per_box'=>$request->input('qty_per_case'.$cnt),
                                'no_boxs'=>$request->input('no_of_case'.$cnt),
                                'total'=>$request->input('total'.$cnt)
                     );

            $shipment_detail= new Shipment_detail($shipment);
            $shipment_detail->save();
        }

        return redirect('order/shipment')->with('Success','Shipment Information Added Successfully');
    }
}
