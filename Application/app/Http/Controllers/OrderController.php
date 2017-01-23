<?php
namespace App\Http\Controllers;
use App\Amazon_destination;
use App\Amazon_inventory;
use App\Listing_service;
use App\Listing_service_detail;
use App\Outbound_method;
use App\Prep_detail;
use App\Prep_service;
use App\Product_labels;
use App\Supplier_detail;
use App\Shipping_method;
use App\Http\Requests\ShipmentRequest;
use App\Shipment_detail;
use App\Supplier;
use App\Supplier_inspection;
use App\Product_labels_detail;
use App\Shipments;
use App\Order;
use App\User_credit_cardinfo;
use App\Addresses;
use App\Http\Middleware\Amazoncredential;
use App\Outbound_Shipping_detail;
use App\Payment_detail;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentCard;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth',Amazoncredential::class]);
    }
    public function index()
    {
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Completed');
        return view('order.index')->with(compact('orders','orderStatus'));
    }
    public function removeorder(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Listing_service_detail::where('order_id',$post['order_id'])->delete();
            Prep_detail::where('order_id',$post['order_id'])->delete();
            Product_labels_detail::where('order_id',$post['order_id'])->delete();
            Supplier_detail::where('order_id',$post['order_id'])->delete();
            Shipments::where('order_id',$post['order_id'])->delete();
            Order::where('order_id',$post['order_id'])->delete();
            return 1;
        }
    }
    public function shipment(Request $request)
    {
        //Remove session
        $request->session()->forget('order_id');
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment=array();
        return view('order.shipment')->with(compact('shipping_method','product','shipment','orders'));
    }
    public function updateshipment(Request $request)
    {
        //print_r($_GET);exit;
        if(!empty($request->order_id)){
            $request->session()->put('order_id', $request->order_id);


        }
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment= Shipments::where('order_id',$order_id)->get();
        $shipment_detail = Shipment_detail::selectRaw("shipment_details.* ")
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->where('shipments.order_id',$order_id)
            ->where('shipments.is_activated','0')
            ->get();
        return view('order.shipment')->with(compact('shipping_method','product','shipment','shipment_detail'));
    }
    public function addshipment(ShipmentRequest $request)
    {
         $user = \Auth::user();
         if(empty($request->input('order_id')))
         {
            $order_detail=array('order_no'=>Uuid::generate(1,time())->string,
                                'user_id'=>$user->id
             );
             $order = new Order($order_detail);
             $order->save();
             $order_id = $order->order_id;
         }
         else
         {
             $order_id=$request->input('order_id');
         }

         $request->session()->put('order_id', $order_id);

        if($request->input('split_shipment')=='0') {
            if (!empty($request->input('shipment_id2'))) {
                $sub_count = $request->input('count2');
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input("shipment_detail2_" . $sub_cnt))) {
                        $shipment_detail_id=$request->input("shipment_detail2_" . $sub_cnt);
                        Listing_service_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Prep_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Product_labels_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Supplier_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Shipment_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                    }
                }
                Shipments::where('shipment_id',$request->input('shipment_id2'))->delete();
            }
        }

         for ($cnt = 1; $cnt <= $request->input('ship_count'); $cnt++) {
            if(!empty($request->input('shipment_id'.$cnt)))
            {
                $shipment = array('order_id'=>$order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
                $sub_count=$request->input('count'.$cnt);
                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
                    if (!empty($request->input("shipment_detail" . $cnt . "_" . $sub_cnt))) {
                        $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                        $shipment_details = array(
                            'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                            'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                            'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                            'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                            'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                        );

                        Shipment_detail::where('shipment_detail_id', $request->input("shipment_detail" . $cnt . "_" . $sub_cnt))->update($shipment_details);
                    }
                    else
                    {
                        if(!empty($request->input('product_desc'.$cnt."_".$sub_cnt))) {
                            $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                            $shipment_details = array('shipment_id' => $request->input('shipment_id' . $cnt),
                                'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                                'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                                'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                                'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                                'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                            );
                            $shipment_detail = new Shipment_detail($shipment_details);
                            $shipment_detail->save();
                        }
                    }
                }

            }
            else {
                $shipment = array('order_id'=>$order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                $shipment = new Shipments($shipment);
                $shipment->save();
                $last_id = $shipment->shipment_id;
                $sub_count=$request->input('count'.$cnt);
                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
                    if(!empty($request->input('product_desc'.$cnt."_".$sub_cnt))) {
                        $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                        $shipment_details = array('shipment_id' => $last_id,
                            'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                            'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                            'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                            'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                            'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                        );
                        $shipment_detail = new Shipment_detail($shipment_details);
                        $shipment_detail->save();
                    }
                }
            }
        }
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
    }
    public function removeproduct(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Listing_service_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Prep_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Product_labels_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Supplier_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Shipment_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
        }

    }
    public function supplierdetail(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $product = Shipment_detail::selectRaw("shipments.order_id, shipment_details.shipment_detail_id,supplier_details.supplier_id,  supplier_details.supplier_detail_id,  shipment_details.product_id, shipment_details.total,  amazon_inventories.product_name  ")
            ->join('supplier_details','shipment_details.shipment_detail_id','=','supplier_details.shipment_detail_id','left')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        $supplier = Supplier::where('user_id',$user->id)->get();
        return view('order.supplier')->with(compact('product', 'supplier'));
    }
    public function addsupplierdetail(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('supplier_detail_id'.$cnt))) {
                $supplier = array('shipment_detail_id'=>$request->input('shipment_detail_id' . $cnt),
                    'order_id' => $request->input('order_id'),
                    'supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                $supplier_detail = new Supplier_detail($supplier);
                $supplier_detail->save();
            }
            else{
                $supplier = array('supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                Supplier_detail::where('supplier_detail_id',$request->input('supplier_detail_id'.$cnt))->update($supplier);
            }
        }

        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }
    public function addsupplier(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $post = $request->all();
            $supplier = new Supplier();
            $supplier->user_id = $user->id;
            $supplier->company_name = $post['company_name'];
            $supplier->contact_name = $post['contact_name'];
            $supplier->email = $post['email'];
            $supplier->phone_number = $post['phone'];
            $supplier->save();
        }

    }
    public function preinspection(Request $request)
    {

        $order_id = $request->session()->get('order_id');
        $supplier = Supplier::selectRaw("supplier_inspections.is_inspection, supplier_inspections.inspection_decription, suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id','left')
            ->join('supplier_inspections','supplier_details.order_id','=','supplier_inspections.order_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->distinct('supplier_inspections.supplier_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.order_id, supplier_inspections.supplier_inspection_id, supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->join('supplier_inspections','supplier_inspections.order_id','=','supplier_details.order_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->groupby('supplier_details.supplier_detail_id')
            ->get();
        return view('order.pre_inspection')->with(compact('product', 'supplier'));
    }
    public function addpreinspection(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_count=$request->input('product_count'.$cnt);
            for($product_cnt=1; $product_cnt< $product_count; $product_cnt++) {
                if(empty($request->input('supplier_inspection_id'.$cnt."_".$product_cnt))) {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'order_id'=>$request->input('order_id'),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc'.$cnt),
                        'supplier_id' => $request->input('supplier_id'.$cnt)
                    );
                    $supplier_inspection = new Supplier_inspection($supplier);
                    $supplier_inspection->save();
                }
                else
                {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc' . $cnt),
                        'supplier_id' => $request->input('supplier_id'.$cnt)
                    );
                    Supplier_inspection::where('supplier_inspection_id',$request->input('supplier_inspection_id'.$cnt."_".$product_cnt))->update($supplier);
                }
            }
        }
        return redirect('order/productlabels')->with('Success', 'Pre inspection Information Added Successfully');
    }
    public function labels(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $product_label= Product_labels::all();
        $product = Shipment_detail::selectRaw(" shipments.order_id, product_labels_details.product_label_detail_id, product_labels_details.product_label_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('product_labels_details','shipment_details.shipment_detail_id','=','product_labels_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipment_details.shipment_detail_id')
            ->get();
        return view('order.product_labels')->with(compact('product', 'product_label'));
    }
    public function addlabels(ShipmentRequest $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('product_label_detail_id'.$cnt))) {

                $product_label = array('order_id'=>$request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => $request->input('labels' . $cnt),
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt)
                );
                $product_labels_detail = new Product_labels_detail($product_label);
                $product_labels_detail->save();
            }
            else
            {
                $product_label = array(
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => $request->input('labels' . $cnt),
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt)
                );
                Product_labels_detail::where('product_label_detail_id',$request->input('shipment_detail_id'.$cnt))->update($product_label);
            }

        }
        return redirect('order/prepservice')->with('Success', 'Product Label Information Added Successfully');
    }
    public function prepservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $prep_service= Prep_service::all();
        $product = Shipment_detail::selectRaw("shipments.order_id, prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.grand_total, prep_details.prep_service_ids, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('order.prep_service')->with(compact('prep_service', 'product'));
    }
    public function addprepservice(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service=array();
            $sub_count =$request->input('sub_count'.$cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[]=$request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if(empty($request->input('prep_detail_id'.$cnt))) {
                $prep_service = array('user_id' => $user->id,
                    'order_id'=>$request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => implode(',', $service),
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                $prep_service_detail = new Prep_detail($prep_service);
                $prep_service_detail->save();
            }
            else
            {
                $prep_service = array('user_id' => $user->id,
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => implode(',', $service),
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Prep_detail::where('prep_detail_id',$request->input('prep_detail_id'.$cnt))->update($prep_service);
            }
        }
        return redirect('order/listservice')->with('Success', 'Prep Service Information Added Successfully');
    }
    public function listservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $list_service= Listing_service::all();
        $product = Shipment_detail::selectRaw("shipments.order_id, listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.grand_total, listing_service_details.listing_service_ids,shipment_details.product_id, shipment_details.shipment_detail_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->join('listing_service_details','listing_service_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
      return view('order.list_service')->with(compact('list_service', 'product'));
    }
    public function addlistservice(ShipmentRequest $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service=array();
            $sub_count =$request->input('sub_count'.$cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[]=$request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if(empty($request->input('listing_service_detail_id'.$cnt))) {
                $list_service = array('order_id'=>$request->input('order_id'),
                        'product_id' => $request->input('product_id' . $cnt),
                        'listing_service_ids' => implode(',', $service),
                        'shipment_detail_id' => $request->input('shipment_detail_id'.$cnt),
                        'listing_service_total' => $request->input('total' . $cnt),
                        'grand_total' => $request->input('grand_total')
                    );
                    $list_service_detail = new Listing_service_detail($list_service);
                    $list_service_detail->save();

            }
            else
            {
                $list_service = array(
                    'product_id' => $request->input('product_id' . $cnt),
                    'listing_service_ids' => implode(',', $service),
                    'shipment_detail_id' => $request->input('shipment_detail_id'.$cnt),
                    'listing_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Listing_service_detail::where('listing_service_detail_id',$request->input('listing_service_detail_id'.$cnt))->update($list_service);
            }
        }
        return redirect('order/outbondshipping')->with('Success', 'Listing service Information Added Successfully');
    }
    public function outbondshipping(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $outbound_method= Outbound_method::all();
        $amazon_destination = Amazon_destination::all();
        $shipment =Shipments::selectRaw("shipments.*, shipping_methods.shipping_method_id,shipping_methods.shipping_name")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        $product = Shipment_detail::selectRaw("shipments.order_id, shipment_details.product_id, shipment_details.total, shipment_details.shipment_id, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
           ->where('shipments.order_id', $order_id)
            ->get();
        $outbound_detail = Outbound_Shipping_detail::selectRaw("shipments.order_id, outbound_shipping_details.* ")
            ->join('shipments','outbound_shipping_details.shipment_id','=','shipments.shipment_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('order.outbound_shipping')->with(compact('amazon_destination', 'outbound_method', 'shipment', 'product','outbound_detail'));
    }

    public function addoutbondshipping(ShipmentRequest $request)
    {
        $ship_count = $request->input('ship_count');
        for ($ship_cnt = 1; $ship_cnt < $ship_count; $ship_cnt++) {
            $count = $request->input('count' . $ship_cnt);
            for ($cnt = 1; $cnt < $count; $cnt++) {
                $product = array();
                $qty = array();
                $product_count = $request->input("product_count" . $ship_cnt . "_" . $cnt);
                for ($product_cnt = 1; $product_cnt < $product_count; $product_cnt++) {
                    $product[] = $request->input('product_id' . $ship_cnt . "_" . $cnt . "_" . $product_cnt);
                    $qty[] = $request->input('total_unit' . $ship_cnt . "_" . $cnt . "_" . $product_cnt);
                }

                if (empty($request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))) {
                    $outbound_shipping = array("amazon_destination_id" => $request->input('amazon_destination_id' . $ship_cnt . "_" . $cnt),
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_id" => $request->input('shipment_id' . $ship_cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => implode(',', $product),
                        "qty" => implode(',', $qty)
                    );
                    $outbound_shipping_detail = new Outbound_Shipping_detail($outbound_shipping);
                    $outbound_shipping_detail->save();
                } else {
                    $outbound_shipping = array("amazon_destination_id" => $request->input('amazon_destination_id' . $ship_cnt . "_" . $cnt),
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_id" => $request->input('shipment_id' . $ship_cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => implode(',', $product),
                        "qty" => implode(',', $qty)
                    );
                    Outbound_Shipping_detail::where('outbound_shipping_detail_id', $request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))->update($outbound_shipping);
                }
            }
        }
        return redirect('order/reviewshipment')->with('Success', 'Outbound Shipping Information Added Successfully');
    }
    public function reviewshipment(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $shipment = Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, sum(shipment_details.total) as total")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id')
            ->join('shipment_details','shipments.shipment_id','=','shipment_details.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->groupby('shipment_details.shipment_id')
            ->get();
        $outbound_detail= Outbound_Shipping_detail::selectRaw('amazon_destinations.destination_name, outbound_shipping_details.qty, outbound_methods.outbound_name')
            ->join('amazon_destinations','outbound_shipping_details.amazon_destination_id','=','amazon_destinations.amazon_destination_id','left')
            ->join('outbound_methods','outbound_shipping_details.outbound_method_id','=','outbound_methods.outbound_method_id','left')
            ->where('outbound_shipping_details.order_id',$order_id)
            ->get();
        $product_detail= Shipment_detail::selectRaw('shipments.order_id, amazon_inventories.product_name, shipment_details.total, prep_details.prep_service_ids')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
            ->where('shipments.order_id',$order_id)
            ->get();
        $prep_service= Prep_service::all();
        return view('order.review_shipment')->with(compact('shipment','outbound_detail','product_detail','prep_service'));
    }
    public function orderpayment(Request $request){
        $order_id = $request->session()->get('order_id');
        $supplier = Supplier_detail::where('order_id',$order_id)->groupby('supplier_id')->get();
        $supplier_count=count($supplier);
        $pre_shipment_inspection=400*$supplier_count;
        $label=Product_labels_detail::SelectRaw('sum(qty) as total')->where('order_id',$order_id)->groupby('product_label_id')->get();
        $label_total=0;
        foreach ($label as $labels)
        {
            $label_total+=$labels->total*0.15;
        }
        $price=array('pre_shipment_inspection'=>$pre_shipment_inspection,
            'shipping_cost'=>'0',
            'port_fee'=>'0',
            'custom_brokerage'=>'0',
            'custom_duty'=>'0',
            'consult_charge'=>'0',
            'label_charge'=>$label_total,
            'prep_forwarding'=>'4',
            'listing_service'=>'0',
            'inbound_shipping'=>'0',
        );
        $card_type= array('visa'=>'visa',
            'mastercard'=>'mastercard',
            'amex'=>'amex',
            'discover'=>'discover',
            'maestro'=>'maestro'
        );
        $user = \Auth::user();
        $addresses =Addresses::where('user_id', $user->id)->where('type','B')->get();
        $credit_card= User_credit_cardinfo::where('user_id',$user->id)->get();
        return view('order.payment')->with(compact('price','card_type','addresses','credit_card'));
    }
    public function addcreditcard(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    'ATZYtBR5Q78IyeyfBqznRDn-u5cOmbQ4I-F7SliUlBZnLuvJC2CG78casVBs39nzcowPQxh7UQIh9wxk',
                    'EB-7iN9A54Z5f70wUQ6Guau1Wj_Kx94EuhFQveM1qlDRcAG6LmYe-MmDsH53phtBRxVhXyc4U_aOX2bz'
                )
            );
            $date = explode('/',$request->input('expire_card'));
            $card = new CreditCard();
            $card->setType($request->input('credit_card_type'))
                ->setNumber($request->input('credit_card_number'))
                ->setExpireMonth($date[0])
                ->setExpireYear($date[1])
                ->setCvv2($request->input('cvv'))
                ->setFirstName($request->input('first_name'))
                ->setLastName($request->input('last_name'));

            try {
                $card->create($apiContext);
            }
            catch (\PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            }
            catch (Exception $ex) {
                die($ex);
            }
            $card_detail=array('user_id'=>$user->id,
                'credit_card_type'=>$card->type,
                'credit_card_number'=>$card->number,
                'credit_card_id' =>$card->id
            );
            User_credit_cardinfo::create($card_detail);
        }
    }
    public function addaddress(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $address_detail=array('user_id'=>$user->id,
                'type'=>'B',
                'address_1'=>$request->input('address_line_1'),
                'address_2'=>$request->input('address_line_2'),
                'city' =>$request->input('city'),
                'state' =>$request->input('state'),
                'postal_code' =>$request->input('postal_code'),
                'country' => $request->input('country')
            );
            Addresses::create($address_detail);
        }
    }
    public function addorderpayment(Request $request){

        $order_id = $request->session()->get('order_id');
        $payment_detail =array('address_id'=>$request->input('address'),
            'order_id' =>$order_id,
            'user_credit_cardinfo_id'=>$request->input('credit_card_detail'),
            'pre_shipment_inspection'=>$request->input('pre_ship_inspect'),
            'shipping_cost'=>$request->input('shipping_cost'),
            'port_fees'=>$request->input('port_fees'),
            'customs_brokerage'=>$request->input('custom_brokerage'),
            'customs_duty'=>$request->input('custom_duty'),
            'consulting_charge'=>$request->input('consulting'),
            'labels_charge'=>$request->input('label_charge'),
            'prep_forward_charge'=>$request->input('prep_forward'),
            'listing_service_charge'=>$request->input('listing_service'),
            'total_fbaforward_charge'=>$request->input('total_fbaforward'),
            'inbound_shipping_charge'=>$request->input('inbound_shipping'),
            'total_cost'=>$request->input('total_cost')
        );
        Payment_detail::create($payment_detail);
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'ATZYtBR5Q78IyeyfBqznRDn-u5cOmbQ4I-F7SliUlBZnLuvJC2CG78casVBs39nzcowPQxh7UQIh9wxk',
                'EB-7iN9A54Z5f70wUQ6Guau1Wj_Kx94EuhFQveM1qlDRcAG6LmYe-MmDsH53phtBRxVhXyc4U_aOX2bz'
            )
        );
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($request->input('total_cost'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://localhost:8000/order/payment")
            ->setCancelUrl("http://localhost:8000/order/payment");

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        $request = clone $payment;
        try {
            $payment->create($apiContext);

        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData(); // Prints the detailed error message
            die($ex);
        }
        catch (Exception $ex) {
            \ResultPrinter::printError('Create Payment Using Credit Card. If 500 Exception, try creating a new Credit Card using <a href="https://www.paypal-knowledge.com/infocenter/index?page=content&widgetview=true&id=FAQ1413">Step 4, on this link</a>, and using it.', 'Payment', null, $request, $ex);
            exit(1);
        }

        //\ResultPrinter::printResult('Create Payment Using Credit Card', 'Payment', $payment->getId(), $request, $payment);
        echo "<pre>";
        print_r($payment);
        exit;
        return $payment;

    }
}
