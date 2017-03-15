<?php

namespace App\Http\Controllers;

use App\Product_labels;
use App\Shipment_detail;
use App\Product_labels_detail;
use App\Order;
use App\Http\Middleware\Amazoncredential;
use Illuminate\Http\Request;
use App\Libraries;
use PDF;
use Yajra\Datatables\Datatables;
use DNS1D;

class ProductlabelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //For display Label of particular order
    public function index(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $product_label = Product_labels::all();
        $product = Shipment_detail::selectRaw(" shipments.order_id, product_labels_details.price, product_labels_details.product_label_detail_id, product_labels_details.product_label_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.product_nick_name, amazon_inventories.product_nick_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipments', 'shipment_details.shipment_id', '=', 'shipments.shipment_id', 'left')
            ->join('product_labels_details', 'shipment_details.shipment_detail_id', '=', 'product_labels_details.shipment_detail_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipment_details.shipment_detail_id')
            ->get();
        return view('productlabel.product_labels')->with(compact('product', 'product_label'));
    }

    //add labels for particular order
    public function update(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if (empty($request->input('product_label_detail_id' . $cnt))) {
                $product_label_id = explode(' ', $request->input('labels' . $cnt));
                $product_label = array('order_id' => $request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => isset($product_label_id[0]) ? $product_label_id[0] : '',
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt),
                    'price' => $request->input('price' . $cnt)
                );
                $product_labels_detail = new Product_labels_detail($product_label);
                $product_labels_detail->save();
            } else {
                $product_label_id = explode(' ', $request->input('labels' . $cnt));
                $product_label = array(
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => isset($product_label_id[0]) ? $product_label_id[0] : '',
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt),
                    'price' => $request->input('price' . $cnt)
                );
                Product_labels_detail::where('product_label_detail_id', $request->input('product_label_detail_id' . $cnt))->update($product_label);
            }
        }
        $order_detail = array('steps' => '4');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('prepservice')->with('Success', 'Product Label Information Added Successfully');
    }
}
