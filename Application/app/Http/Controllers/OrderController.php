<?php

namespace App\Http\Controllers;

use App\Amazon_inventory;
use App\Supplier_detail;
use Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Shipping_method;
use App\Http\Requests\ShipmentRequest;
use App\Shipment_detail;
use phpDocumentor\Reflection\Types\Integer;
use App\Supplier;
use App\Supplier_inspection;
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
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        return view('order.shipment')->with(compact('shipping_method', 'product'));
    }

    public function addshipment(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt <= $count; $cnt++) {
            $product_id = explode(' ', $request->input('product_desc' . $cnt));
            $shipment = array('user_id' => $user->id,
                'product_id' => $product_id[1],
                'shipping_method_id' => $request->input('shipping_method' . $cnt),
                'split_shipment' => $request->input('split_shipment'),
                'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                'fnsku' => $request->input('upc_fnsku' . $cnt),
                'qty_per_box' => $request->input('qty_per_case' . $cnt),
                'no_boxs' => $request->input('no_of_case' . $cnt),
                'total' => $request->input('total' . $cnt)
            );

            $shipment_detail = new Shipment_detail($shipment);
            $shipment_detail->save();
        }

        return redirect('order/supplier')->with('Success', 'Shipment Information Added Successfully');
    }

    public function supplierdetail()
    {
        $user = \Auth::user();
        $product = Shipment_detail::selectRaw("shipment_details.shipment_detail_id, shipment_details.user_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->get();
        $supplier = Supplier::all();
        return view('order.supplier')->with(compact('product', 'supplier'));
    }

    public function addsupplierdetail(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $supplier = array('supplier_id' => $request->input('supplier' . $cnt),
                'user_id' => $user->id,
                'product_id' => $request->input('product_id' . $cnt),
                'total_unit' => $request->input('total' . $cnt)
            );
            $supplier_detail = new Supplier_detail($supplier);
            $supplier_detail->save();
        }
        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }

    public function addsupplier()
    {
        if (Request::ajax()) {
            $post = Request::all();
            $supplier = new Supplier();
            $supplier->company_name = $post['company_name'];
            $supplier->contact_name = $post['contact_name'];
            $supplier->email = $post['email'];
            $supplier->phone_number = $post['phone'];
            $supplier->save();
        }

    }

    public function preinspection()
    {
        $user = \Auth::user();
        $supplier = Supplier::selectRaw("suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id')
            ->where('supplier_details.user_id', $user->id)
            ->distinct('supplier_details.user_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->where('supplier_details.user_id', $user->id)
            ->get();
        return view('order.pre_inspection')->with(compact('product', 'supplier'));
    }

    public function addpreinspection(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_count=$request->input('product_cnt'.$cnt);
            for($product_cnt=1; $product_cnt< $product_count; $product_cnt++) {
                $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id'.$cnt."_".$product_cnt),
                    'user_id' => $user->id,
                    'is_inspection' => $request->input('inspection'.$cnt),
                    'inspection_decription' => $request->input('inspection_desc'.$cnt)
                );
                $supplier_inspection = new Supplier_inspection($supplier);
                $supplier_inspection->save();
            }
        }
    }
}
