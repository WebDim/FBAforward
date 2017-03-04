<?php
namespace App\Http\Controllers;
use App\Additional_service;
use App\Amazon_marketplace;
use App\Amazon_destination;
use App\Amazon_inventory;
use App\Bill_of_lading;
use App\CFS_terminal;
use App\Charges;
use App\Custom_clearance;
use App\Customer_amazon_detail;
use App\Customer_quickbook_detail;
use App\Delivery_booking;
use App\Dev_account;
use App\Inspection_report;
use App\Invoice_detail;
use App\Listing_service;
use App\Listing_service_detail;
use App\Order_note;
use App\Other_label_detail;
use App\Outbound_method;
use App\Payment_info;
use App\Payment_type;
use App\Photo_list_detail;
use App\Prealert_detail;
use App\Prep_detail;
use App\Prep_service;
use App\Product_labels;
use App\Setting;
use App\Shipping_charge;
use App\Shipping_quote;
use App\Supplier_detail;
use App\Shipping_method;
use App\Shipment_detail;
use App\Supplier;
use App\Supplier_inspection;
use App\Product_labels_detail;
use App\Shipments;
use App\Order;
use App\Trucking_company;
use App\User;
use App\User_credit_cardinfo;
use App\Addresses;
use App\Http\Middleware\Amazoncredential;
use App\Outbound_shipping_detail;
use App\Payment_detail;
use App\User_info;
use App\Warehouse_checkin;
use App\Warehouse_checkin_image;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Tests\A;
use Webpatser\Uuid\Uuid;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use App\Libraries;
use Illuminate\Support\Facades\DB;
use PDF;
use Yajra\Datatables\Datatables;
use DNS1D;
use ZipArchive;
class OrderController extends Controller
{
    private $IntuitAnywhere;
    private $context;
    private $realm;
    public function __construct()
    {
        $this->middleware(['auth',Amazoncredential::class]);
    }
    //list Inprogress, Order Placed or Pending For Approval orders of perticular user
    public function index()
    {
        $title="Order Management";
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated',array('0','1','2','4'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.index')->with(compact('orders','orderStatus','title'));
    }
    //list completed orders of perticular user
    public function orderhistory()
    {
        $title="Order History";
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated',array('14'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.order_history')->with(compact('orders','orderStatus','title'));
    }
    // remove perticular order
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
    // For display shipment view
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
    // For display update shipment view
    public function updateshipment(Request $request)
    {
        if(!empty($request->order_id)){
            $request->session()->put('order_id', $request->order_id);
            $steps=Order::where('order_id',$request->order_id)->get();
            if($steps[0]->steps==2)
                return redirect('order/supplierdetail');
            else if ($steps[0]->steps==3)
                return redirect('order/preinspection');
            else if ($steps[0]->steps==4)
                return redirect('order/productlabels');
            else if ($steps[0]->steps==5)
                return redirect('order/prepservice');
            else if ($steps[0]->steps==6)
                return redirect('order/listservice');
            else if ($steps[0]->steps==7)
                return redirect('order/outbondshipping');
            else if ($steps[0]->steps==8)
                return redirect('order/reviewshipment');
            else if ($steps[0]->steps==9)
                return redirect('order/payment');
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
    // Add or Update Shipment and Shipment Detail
    public function addshipment(Request $request)
    {
        $user = \Auth::user();
        //create order
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
        //delete shipment2
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
            //update shipment and shipment detail
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
                    //every product update
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
                    //new product add in current shipment
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
            //insert shipment and shipment detail
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
        $order_detail=array('steps'=>'1');
        Order::where('order_id',$order_id)->update($order_detail);
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
    }
    //remove perticular product from shipment
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
    //For display supplier information of perticular order
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
    //add suplier for perticular order
    public function addsupplierdetail(Request $request)
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
                $supplier_inspection= array('supplier_id'=>$request->input('supplier' . $cnt),'is_inspection'=>'0','inspection_decription'=>'');
                Supplier_inspection::where('supplier_detail_id',$request->input('supplier_detail_id'.$cnt))->update($supplier_inspection);
            }
        }
        $order_detail=array('steps'=>'2');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }
    //add supplier for perticular user
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
    //For display pre inspection information of perticular order
    public function preinspection(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $supplier = Supplier::selectRaw("supplier_inspections.is_inspection, supplier_inspections.inspection_decription, suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id','left')
            ->join('supplier_inspections','supplier_details.supplier_detail_id','=','supplier_inspections.supplier_detail_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->groupby('suppliers.supplier_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.order_id, supplier_inspections.supplier_inspection_id, supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->join('supplier_inspections','supplier_inspections.supplier_detail_id','=','supplier_details.supplier_detail_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->distinct('supplier_inspections.is_inspection')
            ->get();
        return view('order.pre_inspection')->with(compact('product', 'supplier'));
    }
    //add pre inspection information for perticular order
    public function addpreinspection(Request $request)
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
        $order_detail=array('steps'=>'3');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/productlabels')->with('Success', 'Pre inspection Information Added Successfully');
    }
    //For display Label of perticular order
    public function labels(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $product_label= Product_labels::all();
        $product = Shipment_detail::selectRaw(" shipments.order_id, product_labels_details.price, product_labels_details.product_label_detail_id, product_labels_details.product_label_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('product_labels_details','shipment_details.shipment_detail_id','=','product_labels_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipment_details.shipment_detail_id')
            ->get();
        return view('order.product_labels')->with(compact('product', 'product_label'));
    }
    //add labels for perticular order
    public function addlabels(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('product_label_detail_id'.$cnt))) {
                $product_label_id= explode(' ',$request->input('labels' . $cnt));
                $product_label = array('order_id'=>$request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => isset($product_label_id[0])? $product_label_id[0] : '',
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt),
                    'price' =>$request->input('price'. $cnt)
                );
                $product_labels_detail = new Product_labels_detail($product_label);
                $product_labels_detail->save();
            }
            else
            {
                $product_label_id= explode(' ',$request->input('labels' . $cnt));
                $product_label = array(
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => isset($product_label_id[0])? $product_label_id[0] : '',
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt),
                    'price'=>$request->input('price'. $cnt)
                );
                Product_labels_detail::where('product_label_detail_id',$request->input('product_label_detail_id'.$cnt))->update($product_label);
            }
        }
        $order_detail=array('steps'=>'4');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/prepservice')->with('Success', 'Product Label Information Added Successfully');
    }
    //For display prep service information of perticular order
    public function prepservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $prep_service= Prep_service::all();
        $product = Shipment_detail::selectRaw("other_label_details.other_label_detail_id, other_label_details.label_id, shipments.order_id, prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.grand_total, prep_details.prep_service_ids, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->join('other_label_details','other_label_details.prep_detail_id','=','prep_details.prep_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('order.prep_service')->with(compact('prep_service', 'product'));
    }
    //add prep service for perticular order
    public function addprepservice(Request $request)
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
                foreach ($service as $services) {
                    if ($services == 2) {
                        $other_label = array('label_id'=>$request->input('other_label'.$cnt),
                            'prep_detail_id'=>$prep_service_detail->prep_detail_id
                        );
                        $other_label_detail= new Other_label_detail($other_label);
                        $other_label_detail->save();
                    }
                }
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
                if(empty($request->input('other_label_detail_id'.$cnt))) {
                    foreach ($service as $services) {
                        if ($services == 2) {
                            $other_label = array('label_id' => $request->input('other_label' . $cnt),
                                'prep_detail_id' => $request->input('prep_detail_id' . $cnt)
                            );
                            $other_label_detail= new Other_label_detail($other_label);
                            $other_label_detail->save();
                        }
                    }
                }
                else
                {
                    foreach ($service as $services) {
                        if ($services == 2) {
                            $other_label = array('label_id' => $request->input('other_label' . $cnt),
                                'prep_detail_id' => $request->input('prep_detail_id' . $cnt)
                            );
                            Other_label_detail::where('other_label_detail_id', $request->input('other_label_detail_id' . $cnt))->update($other_label);
                        }
                    }
                }
            }
        }
        $order_detail=array('steps'=>'5');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/listservice')->with('Success', 'Prep Service Information Added Successfully');
    }
    //to remove perticular other label detail from order
    public function removeotherlabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Other_label_detail::where('other_label_detail_id',$post['label_detail_id'])->delete();
        }
    }
    //For display list service information of perticular order
    public function listservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $list_service= Listing_service::all();
        $product = Shipment_detail::selectRaw("photo_list_details.photo_list_detail_id, photo_list_details.standard_photo, photo_list_details.prop_photo, shipments.order_id, listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.grand_total, listing_service_details.listing_service_ids,shipment_details.product_id, shipment_details.shipment_detail_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->join('listing_service_details','listing_service_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->join('photo_list_details','photo_list_details.listing_service_detail_id','=','listing_service_details.listing_service_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('order.list_service')->with(compact('list_service', 'product'));
    }
    //add list services for perticular order
    public function addlistservice(Request $request)
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
                foreach ($service as $services) {
                    if ($services == 1) {
                        $photo_detail = array('listing_service_detail_id'=>$list_service_detail->listing_service_detail_id,
                            'standard_photo'=>$request->input('standard'.$cnt),
                            'prop_photo'=>$request->input('prop'.$cnt)
                        );
                        $photo_list_detail= new Photo_list_detail($photo_detail);
                        $photo_list_detail->save();
                    }
                }
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
                if(empty($request->input('photo_list_detail_id'.$cnt))) {
                    foreach ($service as $services) {
                        if ($services == 1) {
                            $photo_detail = array('listing_service_detail_id'=>$request->input('listing_service_detail_id'.$cnt),
                                'standard_photo'=>$request->input('standard'.$cnt),
                                'prop_photo'=>$request->input('prop'.$cnt)
                            );
                            $photo_list_detail= new Photo_list_detail($photo_detail);
                            $photo_list_detail->save();
                        }
                    }
                }
                else
                {
                    foreach ($service as $services) {
                        if ($services == 1) {
                            $photo_detail = array('listing_service_detail_id'=>$request->input('listing_service_detail_id'.$cnt),
                                'standard_photo'=>$request->input('standard'.$cnt),
                                'prop_photo'=>$request->input('prop'.$cnt)
                            );
                            Photo_list_detail::where('listing_service_detail_id', $request->input('listing_service_detail_id'.$cnt))->update($photo_detail);
                        }
                    }
                }
            }
        }
        $order_detail=array('steps'=>'6');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/outbondshipping')->with('Success', 'Listing service Information Added Successfully');
    }
    //remove perticular photo details of perticular order
    public function removephotolabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Photo_list_detail::where('photo_list_detail_id',$post['photo_list_detail_id'])->delete();
        }
    }
    //For display outbound shipping information of perticular order
    public function outbondshipping(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $outbound_method= Outbound_method::all();
        $shipment =Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, shipments.order_id")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id')
            ->join('shipment_details','shipment_details.shipment_id','=','shipments.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->groupby('shipments.shipment_id')
            ->get();
        $product = Shipment_detail::selectRaw("shipment_details.shipment_id, outbound_shipping_details.outbound_shipping_detail_id,outbound_shipping_details.outbound_method_id, shipment_details.shipment_detail_id, shipments.order_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total,  amazon_inventories.product_name  ")
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('outbound_shipping_details','shipment_details.shipment_detail_id','=','outbound_shipping_details.shipment_detail_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        return view('order.outbound_shipping')->with(compact('outbound_method','product','shipment'));
    }
    // add outbound shipping details of perticular order
    public function addoutbondshipping(Request $request)
    {
        $ship_count = $request->input('ship_count');
        for ($ship_cnt = 1; $ship_cnt < $ship_count; $ship_cnt++) {
            $count = $request->input('count' . $ship_cnt);
            for ($cnt = 1; $cnt < $count; $cnt++) {
                if (empty($request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))) {
                    $outbound_shipping = array(
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_detail_id" => $request->input('shipment_detail_id' . $ship_cnt."_".$cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt ),
                        "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt )
                    );
                    $outbound_shipping_detail = new Outbound_shipping_detail($outbound_shipping);
                    $outbound_shipping_detail->save();
                } else {
                    $outbound_shipping = array(
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_detail_id" => $request->input('shipment_detail_id' . $ship_cnt."_".$cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt),
                        "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt)
                    );
                    Outbound_shipping_detail::where('outbound_shipping_detail_id', $request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))->update($outbound_shipping);
                }
            }
        }
        $order_detail=array('steps'=>'7');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/reviewshipment')->with('Success', 'Outbound Shipping Information Added Successfully');
    }
    //For display review information of perticular order
    public function reviewshipment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $shipment = Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, sum(shipment_details.total) as total")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id')
            ->join('shipment_details','shipments.shipment_id','=','shipment_details.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->groupby('shipment_details.shipment_id')
            ->get();
        $outbound_detail=Outbound_shipping_detail::selectRaw('outbound_shipping_details.qty, amazon_inventories.product_name, outbound_methods.outbound_name')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'outbound_shipping_details.product_ids','left')
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
    //For display payment information of perticular order
    public function orderpayment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $pre_shipment_inspection=Setting::where('key_cd','Pre Shipment Inspection')->get();
        $order_detail=array('steps'=>'8');
        Order::where('order_id',$order_id)->update($order_detail);
        $supplier = Supplier_detail::selectRaw('supplier_details.supplier_id, supplier_inspections.is_inspection')
            ->join('supplier_inspections','supplier_inspections.supplier_id','=','supplier_details.supplier_id')
            ->where('supplier_inspections.order_id',$order_id)
            ->where('supplier_inspections.is_inspection','1')
            ->groupby('supplier_details.supplier_id')->get();
        $supplier_count=count($supplier);
        $pre_shipment_inspection_value=isset($pre_shipment_inspection[0]->value)?$pre_shipment_inspection[0]->value:'0';
        $pre_shipment_inspection_value=$pre_shipment_inspection_value*$supplier_count;
        $label=Product_labels_detail::SelectRaw('sum(price) as total')->where('order_id',$order_id)->groupby('order_id')->get();
        $prep_service=Prep_detail::selectRaw('grand_total')->where('order_id',$order_id)->groupby('order_id')->get();
        $listing_service=Listing_service_detail::selectRaw('grand_total')->where('order_id',$order_id)->groupby('order_id')->get();
        $shipment_fee=Shipping_method::selectRaw("shipments.shipment_id, shipments.shipping_method_id, shipping_methods.port_fee, shipping_methods.custom_brokrage, shipping_methods.consulting_fee")
            ->join('shipments','shipments.shipping_method_id','=','shipping_methods.shipping_method_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        $port_fee=0;
        $custom_brokrage=0;
        $consulting_fee=0;
        foreach ($shipment_fee as $shipment_fees)
        {
            $port_fee=$port_fee+$shipment_fees->port_fee;
            $custom_brokrage=$custom_brokrage+$shipment_fees->custom_brokrage;
            $consulting_fee=$consulting_fee+$shipment_fees->consulting_fee;
        }
        $price=array('pre_shipment_inspection'=>$pre_shipment_inspection_value,
            'shipping_cost'=>'0',
            'port_fee'=>$port_fee,
            'custom_brokerage'=>$custom_brokrage,
            'custom_duty'=>'0',
            'consult_charge'=>$consulting_fee,
            'label_charge'=>isset($label[0]->total)?$label[0]->total:'0',
            'prep_forwarding'=>isset($prep_service[0]->grand_total)?$prep_service[0]->grand_total:'0',
            'listing_service'=>isset($listing_service[0]->grand_total)?$listing_service[0]->grand_total:'0',
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
    //add credit card information for perticular user
    public function addcreditcard(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    env('CLIENT_ID'),
                    env('SECRET_KEY')
                )
            );
            $date = explode('-',$request->input('expire_card'));
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
    //add billing address for perticular user
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
    //add payment detail of perticular order
    public function addorderpayment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $credit_card_detail=explode(' ',$request->input('credit_card_detail'));
        $payment_detail =array('address_id'=>$request->input('address'),
            'order_id' =>$order_id,
            'user_credit_cardinfo_id'=>isset($credit_card_detail[0])?$credit_card_detail[0]:'',
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
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('CLIENT_ID'),
                env('SECRET_KEY')
            )
        );
        $creditCardToken = new CreditCardToken();
        $creditCardToken->setCreditCardId($credit_card_detail[1]);
        $fi = new FundingInstrument();
        $fi->setCreditCardToken($creditCardToken);
        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($request->input('total_cost'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));
        $request = clone $payment;
        try {
            $payment->create($apiContext);
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData();
            die($ex);
        }
        catch (Exception $ex) {
            ResultPrinter::printError("Create Payment using Saved Card", "Payment", null, $request, $ex);
            exit(1);
        }
        $payment_detail_id=Payment_detail::create($payment_detail);
        $last_id=$payment_detail_id->payment_detail_id;
        $payment_info=array('payment_detail_id'=>$last_id,
            'transaction'=>$payment
        );
        Payment_info::create($payment_info);
        $order_detail=array('is_activated'=>'1','steps'=>'9');
        Order::where('order_id',$order_id)->update($order_detail);
        return redirect('order/index')->with('success','Your order Successfully Placed');
    }
    //to display whole information of perticular order
    public function orderDetails(Request $request){
        $title="Order Detail";
        if($request->order_id) {
            if($request->user_id)
            {
                $user_id=$request->user_id;
            }
            else {
                $user = \Auth::user();
                $user_id=$user->id;
                $user_role=$user->role_id;
                $id=$request->id;
            }
            DB::enableQueryLog();
            $shipment_detail = Shipments::selectRaw("shipments.shipment_id,shipments.shipping_method_id,shipping_methods.shipping_name,shipment_details.product_id, shipment_details.fnsku, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total,amazon_inventories.product_name,supplier_details.supplier_detail_id,supplier_details.supplier_id,suppliers.company_name,supplier_inspections.inspection_decription,product_labels_details.product_label_id,product_labels.label_name,prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.prep_service_ids,listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.listing_service_ids,outbound_shipping_details.amazon_destination_id, outbound_shipping_details.outbound_method_id,outbound_methods.outbound_name,amazon_destinations.destination_name")
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id','left')
                ->join('shipment_details','shipment_details.shipment_id','=','shipments.shipment_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('supplier_details','shipment_details.shipment_detail_id','=','supplier_details.shipment_detail_id','left')
                ->join('suppliers','suppliers.supplier_id','=','supplier_details.supplier_id','left')
                ->join('supplier_inspections','supplier_inspections.supplier_detail_id','=','supplier_details.supplier_detail_id','left')
                ->join('product_labels_details','product_labels_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('product_labels','product_labels.product_label_id','=','product_labels_details.product_label_id','left')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('listing_service_details','listing_service_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('outbound_shipping_details','outbound_shipping_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('outbound_methods','outbound_methods.outbound_method_id','=','outbound_shipping_details.outbound_method_id','left')
                ->join('amazon_destinations','amazon_destinations.amazon_destination_id','=','outbound_shipping_details.amazon_destination_id','left')
                ->where('shipments.order_id',$request->order_id)
                ->where('shipments.user_id',$user_id)
                ->orderBy('shipments.shipment_id', 'ASC')
                ->get()->toArray();
            foreach($shipment_detail as $key=>$shipment_details){
                //Fetch Prep services name
                $prep_service_ids = explode(",",$shipment_details['prep_service_ids']);
                $prep_services = Prep_service::selectRaw("service_name")->whereIn('prep_service_id', $prep_service_ids)->get();
                $service_name = array();
                if(count($prep_services)>0) {
                    foreach ($prep_services as $prep_service) {
                        $service_name[] = $prep_service->service_name;
                    }
                }
                $shipment_detail[$key]['prep_service_name'] = implode($service_name, ",");
                //Fetch Listing services name
                $listing_service_ids = explode(",",$shipment_details['listing_service_ids']);
                $listing_services = Listing_service::selectRaw("service_name")->whereIn('listing_service_id', $listing_service_ids)->get();
                $listing_service_name = array();
                if(count($listing_services)>0) {
                    foreach ($listing_services as $listing_service) {
                        $listing_service_name[] = $listing_service->service_name;
                    }
                }
                $shipment_detail[$key]['listing_service_name'] = implode($listing_service_name, ",");
            }
            // Payment Info get
            $payment_detail = Payment_detail::selectRaw('payment_details.*,user_credit_cardinfos.credit_card_number,user_credit_cardinfos.credit_card_type,user_credit_cardinfos.credit_card_id,payment_infos.transaction')
                ->join('payment_infos','payment_infos.payment_detail_id','=','payment_details.payment_detail_id','left')
                ->join('user_credit_cardinfos','user_credit_cardinfos.id','=','payment_details.user_credit_cardinfo_id','left')
                ->where('order_id',$request->order_id)->first();
            if(count($payment_detail)>0)
                $payment_detail = $payment_detail->toArray();
            return view('order.detail_list')->with(compact('shipment_detail','payment_detail','user_role','id','title'));
        }
    }
    //change order status of perticular order
    public function orderstatus(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $status=array('is_activated'=>$post['status']);
            Order::where('order_id',$post['order_id'])->update($status);
        }
    }
    //list Approved orders of All users for warhouse manager
    public function ordershipping()
    {
        $title='Ship Order';
        $orders = Order::where('is_activated','3')->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','title'));
    }
    //list orders of All users which select inspections, uploading inspection report by inspector
    public function inspectionreport()
    {
        $title="Inspection Report";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::selectRaw('orders.*')
            ->join('supplier_inspections','supplier_inspections.order_id','=','orders.order_id')
            ->where('orders.is_activated','1')
            ->where('supplier_inspections.is_inspection','1')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    // to upload inspection report
    public function uploadinspectionreport(Request $request)
    {
        $order_id=$request->input('order_id');
        if ($request->hasFile('report')) {
            $destinationPath = public_path() . '/uploads/reports';
            $image =  $order_id.'_'.'inspectionreport'.'.' . $request->file('report')->getClientOriginalExtension();
            $request->file('report')->move($destinationPath, $image);
            $inpection_data= array('order_id'=>$order_id,
                'uploaded_file'=>$image,
                'status'=>'0'
            );
            Inspection_report::create($inpection_data);
            $data= array('is_activated'=>'2');
            Order::where('order_id',$order_id)->update($data);
            return redirect('order/inspectionreport')->with('success','Report successfully uploaded');
        }
    }
    //to download inspection report
    public function downloadreport(Request $request)
    {
        $order_id=$request->order_id;
        $inspection=Inspection_report::where('order_id',$order_id)->get();
        if(!empty($inspection[0]->uploaded_file)) {
            $inspection_file = isset($inspection[0]->uploaded_file) ? $inspection[0]->uploaded_file : '';
            $file = public_path() . "/uploads/reports/" . $inspection_file;
            $headers = array('Content-Type: application/pdf',
            );
            return response()->download($file, $inspection_file, $headers);
        }
    }
    //inspection report approve by customer
    public function approvereport(Request $request)
    {
        if($request->ajax()){
            $post=$request->all();
            $order_id = $post['order_id'];
            $inpection_data = array('status' => '1');
            Inspection_report::where('order_id', $order_id)->update($inpection_data);
            $data = array('is_activated' => '3');
            Order::where('order_id', $order_id)->update($data);
        }
    }
    //list of all orders which needs upload shipping quote by shipper
    public function shippingquote()
    {
        $title="Shipping Quote";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $details=Order::selectRaw('orders.order_id')
            ->join('supplier_inspections','supplier_inspections.order_id','=','orders.order_id')
            ->where('orders.is_activated','1')
            ->where('supplier_inspections.is_inspection','0')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->get();
        $order_ids=array();
        foreach ($details as $detail)
        {
            $order_ids[]=$detail->order_id;
        }
        if(!empty($order_ids))
            $orders = Order::where('orders.is_activated','3')->orWhereIn('orders.order_id',$order_ids)->orderBy('orders.created_at', 'desc')->get();
        else
            $orders = Order::where('orders.is_activated','3')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
   // to display shippingquote form
    public function shippingquoteform(Request $request)
    {
        $title="Shipping Quote Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::where('order_id',$order_id)->get();
        $charges=Charges::all();
        $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('orders','orders.order_id','=','shipments.order_id','left')
            ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.order_id',$order_id)
            ->get();
        return view('order.shippingquote')->with(compact('order_id','shipping_method','shipment','charges','shipment_detail','user','title'));
    }
    // to add shipping quote form details
    public function addshippingquoteform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            $shipping_quote=array('order_id'=>$request->input('order_id'),
                'shipment_id'=>$request->input('shipment_id'.$cnt),
                'shipment_port'=>$request->input('shipping_port'.$cnt),
                'shipment_term'=>$request->input('shipping_term'.$cnt),
                'shipment_weights'=>$request->input('weight'.$cnt),
                'chargable_weights'=>$request->input('chargable_weight'.$cnt),
                'cubic_meters'=>$request->input('cubic_meter'.$cnt),
                'total_shipping_cost'=>$request->input('total_shipping_cost'.$cnt),
                'status'=>'0'
            );
            $shipping_quote_detail=Shipping_quote::create($shipping_quote);
            $sub_count=$request->input('sub_count'.$cnt);
            for($sub_cnt=1;$sub_cnt<=$sub_count; $sub_cnt++)
            {
                if(!empty($request->input('charges'.$cnt."_".$sub_cnt))) {
                    $shipping_charges = array('shipping_id' => $shipping_quote_detail->id,
                        'charges_id' => $request->input('charges' . $cnt . "_" . $sub_cnt)
                    );
                    Shipping_charge::create($shipping_charges);
                }
            }
        }
        $order= array('is_activated'=>'4');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/shippingquote')->with('success','Shipping Quote Submitted Successfully');
    }
    //to download shipping quote
    public function viewshippingquote(Request $request)
    {
        $order_id=$request->order_id;
        $shipment=Shipments::selectRaw('shipping_quotes.*')
            ->join('shipping_quotes','shipping_quotes.shipment_id','=','shipments.shipment_id')
            ->where('shipments.order_id',$order_id)->get();
        $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('orders','orders.order_id','=','shipments.order_id','left')
            ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.order_id',$order_id)
            ->distinct('shipment_quotes.shipment_id')
            ->get();
        $charges = Charges::selectRaw('charges.name, charges.price, shipping_quotes.shipment_id')
            ->join('shipping_charges','shipping_charges.charges_id','=','charges.id')
            ->join('shipping_quotes','shipping_quotes.id','=','shipping_charges.shipping_id')
            ->where('shipping_quotes.order_id',$order_id)
            ->get();
        view()->share('shipment',$shipment);
        view()->share('shipment_detail',$shipment_detail);
        view()->share('charges',$charges);
        $pdf = PDF::loadView('order/viewshippingquote');
        return $pdf->download('viewshippingquote.pdf');
        /*if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::selectRaw('shipping_quotes.*')
                ->join('shipping_quotes','shipping_quotes.shipment_id','=','shipments.shipment_id')
                ->where('shipments.order_id',$order_id)->get();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->where('orders.order_id',$order_id)
                ->distinct('shipment_quotes.shipment_id')
                ->get();
            $charges = Charges::selectRaw('charges.name, charges.price, shipping_quotes.shipment_id')
                ->join('shipping_charges','shipping_charges.charges_id','=','charges.id')
                ->join('shipping_quotes','shipping_quotes.id','=','shipping_charges.shipping_id')
                ->where('shipping_quotes.order_id',$order_id)
                ->get();
            return view('order/viewshippingquote')->with(compact('shipment','shipment_detail','charges'));
        }*/
    }
    // to approve shipping quote by customer
    public function approveshippingquote(Request $request)
    {
        if($request->ajax()){
            $post=$request->all();
            $order_id = $post['order_id'];
            $shipping_quotes_data = array('status' => '1');
            Shipping_quote::where('order_id', $order_id)->update($shipping_quotes_data);
            /*$data = array('is_activated' => '5');
            Order::where('order_id', $order_id)->update($data);*/
            $this->createCustomer($order_id);
            return 1;
        }
    }
    // quickbook for send automated invoice
    public function  qboConnect(){
        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
        if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), env('QBO_TENANT')) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), env('QBO_TENANT'))) {
            // Set up the IPP instance
            $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), env('QBO_TENANT'));
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                env('QBO_USERNAME'),
                $creds);
            if (env('QBO_SANDBOX')) {
                // Turn on sandbox mode/URLs
                $IPP->sandbox(true);
            }
            // This is our current realm
            $this->realm = $creds['qb_realm'];
            // Load the OAuth information from the database
            $this->context = $IPP->context();
            return true;
        } else {
            return false;
        }
    }
    public function createCustomer($order_id){
        $user__detail= User::selectRaw('user_infos.*')
            ->join('orders','users.id','=','orders.user_id')
            ->join('user_infos','user_infos.user_id','=','users.id')
            ->where('orders.order_id',$order_id)
            ->get();
        $exist_user_detail= Customer_quickbook_detail::selectRaw('customer_quickbook_details.*')
            ->join('orders','customer_quickbook_details.user_id','=','orders.user_id')
            ->where('orders.order_id',$order_id)
            ->get();

        $this->qboConnect();
        $CustomerService = new \QuickBooks_IPP_Service_Customer();
        $Customer = new \QuickBooks_IPP_Object_Customer();
        if(count($exist_user_detail)==0) {
            $Customer->setDisplayName($user__detail[0]->company_name . mt_rand(0, 1000));
            // Terms (e.g. Net 30, etc.)
            //$Customer->setSalesTermRef(4);
            // Phone #
            $PrimaryPhone = new \QuickBooks_IPP_Object_PrimaryPhone();
            $PrimaryPhone->setFreeFormNumber($user__detail[0]->company_phone);
            $Customer->setPrimaryPhone($PrimaryPhone);
            // Mobile #
            $Mobile = new \QuickBooks_IPP_Object_Mobile();
            $Mobile->setFreeFormNumber($user__detail[0]->company_phone);
            $Customer->setMobile($Mobile);
            // Fax #
            $Fax = new \QuickBooks_IPP_Object_Fax();
            $Fax->setFreeFormNumber($user__detail[0]->company_phone);
            $Customer->setFax($Fax);
            // Bill address
            $BillAddr = new \QuickBooks_IPP_Object_BillAddr();
            $BillAddr->setLine1($user__detail[0]->company_address);
            $BillAddr->setLine2($user__detail[0]->company_address2);
            $BillAddr->setCity($user__detail[0]->company_city);
            $BillAddr->setCountrySubDivisionCode($user__detail[0]->company_country);
            $BillAddr->setPostalCode($user__detail[0]->company_zipcode);
            $Customer->setBillAddr($BillAddr);
            // Email
            $PrimaryEmailAddr = new \QuickBooks_IPP_Object_PrimaryEmailAddr();
            $PrimaryEmailAddr->setAddress($user__detail[0]->contact_email);
            $Customer->setPrimaryEmailAddr($PrimaryEmailAddr);
            if ($resp = $CustomerService->add($this->context, $this->realm, $Customer)) {
                $resp = $this->getId($resp);
                $user_data = array('user_id' => $user__detail[0]->user_id,
                    'customer_id' => $resp
                );
                Customer_quickbook_detail::create($user_data);
                $this->addInvoice($resp, $order_id);
            } else {
                //echo 'Not Added qbo';
                print($CustomerService->lastError($this->context));
            }
        }
        else
        {
            $resp=$exist_user_detail[0]->customer_id;
            $this->addInvoice($resp, $order_id);
        }
    }
    public function addItem($cust_resp,$order_id){
        $product=Amazon_inventory::selectRaw('amazon_inventories.*')
            ->join('shipment_details','shipment_details.product_id','=','amazon_inventories.id','left')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        if(isset($product)) {
            $ItemService = new \QuickBooks_IPP_Service_Item();
            foreach ($product as $Item) {
                $items = $ItemService->query($this->context, $this->realm, "SELECT * FROM Item WHERE Name = '$Item->product_name'  ORDER BY Metadata.LastUpdatedTime ");
                $resp[] = $this->getId($items[0]->getId());
            }
            $this->addInvoice($resp, $cust_resp, $order_id);
        }
        else {
            $ItemService = new \QuickBooks_IPP_Service_Item();
            $Item = new \QuickBooks_IPP_Object_Item();
            $Item->setName('ttthello');
            $Item->setType('NonInventory');
            $Item->setIncomeAccountRef('53');
            if ($resp = $ItemService->add($this->context, $this->realm, $Item)) {
                $resp[] = $this->getId($resp);
                //return $this->getId($resp);
            } else {
                print($ItemService->lastError($this->context));
            }
            $this->addInvoice(\GuzzleHttp\json_encode($resp), $cust_resp, $order_id);
        }
    }
    public function addInvoice($cust_resp,$order_id){
        $details= Payment_detail::where('order_id',$order_id)->get();
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $Invoice = new \QuickBooks_IPP_Object_Invoice();
        $Invoice->setDocNumber('WEB' . mt_rand(0, 10000));
        $Invoice->setTxnDate('2013-10-11');
        $product=Amazon_inventory::selectRaw('amazon_inventories.*')
            ->join('shipment_details','shipment_details.product_id','=','amazon_inventories.id','left')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        $service=array('Sea Freight Shipping from China - Dog','Training Collars','Customs Brokerage Fees','U.S. Port Fees','Container Delivery Fee','Wire Transfer Fee');
        if(isset($service)) {
            $ItemService = new \QuickBooks_IPP_Service_Item();
            foreach ($service as $services) {
                $Line = new \QuickBooks_IPP_Object_Line();
                $Line->setDetailType('SalesItemLineDetail');
                $Line->setAmount($details[0]->total_cost);
                $Line->setDescription('');
                $items = $ItemService->query($this->context, $this->realm, "SELECT * FROM Item WHERE Name = '$services'  ORDER BY Metadata.LastUpdatedTime ");
                if(!empty($items)) {
                    $resp = $this->getId($items[0]->getId());
                }
                else
                {
                    $Item = new \QuickBooks_IPP_Object_Item();
                    $Item->setName($services);
                    $Item->setType('Service');
                    $Item->setIncomeAccountRef('10');
                    if ($resp = $ItemService->add($this->context, $this->realm, $Item)) {
                        $resp = $this->getId($resp);
                        //return $this->getId($resp);
                    } else {
                        print($ItemService->lastError($this->context));
                    }
                }
                $SalesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
                $SalesItemLineDetail->setUnitPrice();
                $SalesItemLineDetail->setQty(2);
                $SalesItemLineDetail->setItemRef($resp);
                $Line->addSalesItemLineDetail($SalesItemLineDetail);
                $Invoice->addLine($Line);
            }
            $Invoice->setCustomerRef($cust_resp);
//            $this->addInvoice($resp, $cust_resp, $order_id);
        }
        else{
            exit;
        }
        if ($resp = $InvoiceService->add($this->context, $this->realm, $Invoice))
        {
            $resp=$this->getId($resp);
            $this->invoice_pdf($order_id);
        }
        else
        {
            print($InvoiceService->lastError());
        }
    }
    public function invoice_pdf($order_id)
    {
        //$this->qboConnect();
        $Context=$this->context;
        $realm=$this->realm;
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 1");
        $invoice = reset($invoices);
        $id = substr($invoice->getId(), 2, -1);
        $data = array('is_activated' => '6');
        Order::where('order_id', $order_id)->update($data);
        header("Content-Disposition: attachment; filename=".$order_id."_invoice.pdf");
        header("Content-type: application/x-pdf");
        $dir=public_path() ."/uploads/bills/";
        file_put_contents($dir.$order_id."_invoice.pdf", $InvoiceService->pdf($Context, $realm, $id));
    }
    public function getId($resp){
        $resp = str_replace('{','',$resp);
        $resp = str_replace('}','',$resp);
        $resp = abs($resp);
        return $resp;
    }
    //list of all orders which needs upload bill of lading by shipper
    public function billoflading()
    {
        $title="Bill of Lading";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','6')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    //to display bill of lading form
    public function billofladingform(Request $request)
    {
        $title="Bill Of Lading Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::where('order_id',$order_id)->get();
        $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('orders','orders.order_id','=','shipments.order_id','left')
            ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.order_id',$order_id)
            ->get();
        return view('order.billoflading')->with(compact('order_id','shipment','shipment_detail','user','title'));
    }
    // to add bill of lading details
    public function addbillofladingform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            if ($request->hasFile('bill'.$cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $image = $request->input('order_id') . '_' . $request->input('shipment_id'.$cnt) . '_' . 'lading_bill' . '.' . $request->file('bill'.$cnt)->getClientOriginalExtension();
                $request->file('bill'.$cnt)->move($destinationPath, $image);
                $bill_detail = array('order_id' => $request->input('order_id'),
                    'shipment_id' => $request->input('shipment_id' . $cnt),
                    'sbnumber' => $request->input('ref_number' . $cnt),
                    'bill' => $image,
                    'status' => '0'
                );
                Bill_of_lading::create($bill_detail);
            }
        }
        $order= array('is_activated'=>'7');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/billoflading')->with('success','Bill of Lading Uploaded Successfully');
    }
    // display list of order which need approve for bill of lading by logistics
    public function billofladingapprove()
    {
        $title="Bill Of Lading";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','7')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    //display detail of bill of lading to logistic
    public function viewbilloflading(Request $request)
    {
        if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::selectRaw('bill_of_ladings.*')
                ->join('bill_of_ladings','bill_of_ladings.shipment_id','=','shipments.shipment_id')
                ->where('shipments.order_id',$order_id)->get();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->where('orders.order_id',$order_id)
                ->get();
            return view('order/viewbilloflading')->with(compact('shipment','shipment_detail','order_id'));
        }
    }
    //to download bill of lading
    public function downloadladingbill(Request $request)
    {
        $order_id=$request->order_id;
        $shipment_id=$request->shipment_id;
        $ladingbill=Bill_of_lading::where('order_id',$order_id)->where('shipment_id',$shipment_id)->get();
        $bill=isset($ladingbill[0]->bill)?$ladingbill[0]->bill:'';
        $file= public_path(). "/uploads/bills/".$bill;
        $headers = array('Content-Type: application/pdf',
        );
        return response()->download($file,$bill, $headers);
    }
    //approve bill of lading by logistics
    public function approvebilloflading(Request $request)
    {
        if($request->ajax()){
            $post=$request->all();
            $order_id = $post['order_id'];
            $ladingbill = array('status' => '1');
            Bill_of_lading::where('order_id', $order_id)->update($ladingbill);
            $data = array('is_activated' => '8');
            Order::where('order_id', $order_id)->update($data);
            //$this->createCustomer($order_id);
        }
    }
    //list of all orders which needs upload shipment pre alert by shipper
    public function prealert()
    {
        $title="Shipment Pre Alert";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','8')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    //to display pre alert form
    public function prealertform(Request $request)
    {
        $title="Shipment Pre Alert Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('shipments.order_id',$order_id)
            ->orderby('shipments.shipment_id','asc')
            ->get();
        return view('order.prealert')->with(compact('order_id','shipment','user','title'));
    }
    // add prealert form details
    public function addprealertform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            if ($request->hasFile('ISF'.$cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $isfimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'ISF' . '.' . $request->file('ISF' . $cnt)->getClientOriginalExtension();
                $request->file('ISF' . $cnt)->move($destinationPath, $isfimage);
            }
            if ($request->hasFile('HBL'.$cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $hblimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'HBL' . '.' . $request->file('HBL' . $cnt)->getClientOriginalExtension();
                $request->file('HBL' . $cnt)->move($destinationPath, $hblimage);
            }
            if ($request->hasFile('MBL'.$cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $mblimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'MBL' . '.' . $request->file('MBL' . $cnt)->getClientOriginalExtension();
                $request->file('MBL' . $cnt)->move($destinationPath, $mblimage);
            }
            $prealert_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'ISF' => $isfimage,
                'HBL' => $hblimage,
                'MBL' => $mblimage,
                'ETD_china' => $request->input('ETD_china'.$cnt),
                'ETA_US' => $request->input('ETA_US'.$cnt),
                'delivery_port' => $request->input('delivery_port'.$cnt),
                'status' => '0'
            );
            Prealert_detail::create($prealert_detail);
        }
        $order= array('is_activated'=>'9');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/prealert')->with('success','Shipment Pre Alert Submitted Successfully');
    }
    //display list of orders which need custom clearnce by logistics
    public function customclearance()
    {
        $title="Custom Clearance";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','9')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    //to display custom clearance form
    public function customclearanceform(Request $request)
    {
        $title="Custom Clearance Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('shipments.order_id',$order_id)
            ->orderby('shipments.shipment_id','asc')
            ->get();
        return view('order.customclearance')->with(compact('order_id','shipment','user','title'));
    }
    //  to add custom clearance form detail
    public function addcustomclearanceform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            if ($request->hasFile('form_3461'.$cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $form_3461image = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'form_3461' . '.' . $request->file('form_3461' . $cnt)->getClientOriginalExtension();
                $request->file('form_3461' . $cnt)->move($destinationPath, $form_3461image);
            }
            if ($request->hasFile('form_7501'.$cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $form_7501image = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'form_7501' . '.' . $request->file('form_7501' . $cnt)->getClientOriginalExtension();
                $request->file('form_7501' . $cnt)->move($destinationPath, $form_7501image);
            }
            if ($request->hasFile('delivery_order'.$cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $delivery_orderimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'delivery_order' . '.' . $request->file('delivery_order' . $cnt)->getClientOriginalExtension();
                $request->file('delivery_order' . $cnt)->move($destinationPath, $delivery_orderimage);
            }
            $custom_clearance_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'form_3461' => $form_3461image,
                'form_7501' => $form_7501image,
                'delivery_order' => $delivery_orderimage,
                'custom_duty' => $request->input('custom_duty'.$cnt),
                'terminal_fee' => $request->input('terminal_fee'.$cnt),
                'status' => '0'
            );
            $detail=Custom_clearance::create($custom_clearance_detail);
            for($sub_cnt=1;$sub_cnt<=3; $sub_cnt++)
            {
                if(!empty($request->input('addition_service'.$cnt."_".$sub_cnt))) {
                    $additional_service = array('custom_clearance_id' => $detail->id,
                        'service_id' => $request->input('addition_service' . $cnt . "_" . $sub_cnt)
                    );
                    Additional_service::create($additional_service);
                }
            }
        }
        $order= array('is_activated'=>'10');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/customclearance')->with('success','Custom Clearance Submitted Successfully');
    }
    //list of order which need delivery booking
    public function deliverybooking()
    {
        $title="Delivery Booking";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','10')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    //to display delivery booking form
    public function deliverybookingform(Request $request)
    {
        $title="Delivery Booking Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('shipments.order_id',$order_id)
            ->orderby('shipments.shipment_id','asc')
            ->get();
        $payment_type=Payment_type::all();
        $trucking_company=Trucking_company::all();
        $cfs_terminal=CFS_terminal::all();
        return view('order.delivery_booking')->with(compact('order_id','shipment','user','payment_type','trucking_company','cfs_terminal','title'));
    }
    // to add delivery booking form details
    public function adddeliverybookingform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            $delivery_booking_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'CFS_terminal' => $request->input('CFS_terminal'.$cnt),
                'trucking_company' => $request->input('trucking_company'.$cnt),
                'warehouse_fee' => $request->input('warehouse_fee'.$cnt),
                'fee_paid' => $request->input('fee_paid'.$cnt),
                'ETA_warehouse' => date('Y-m-d H:i:s', strtotime($request->input('ETA_warehouse'.$cnt))),
                'status' => '0'
            );
            Delivery_booking::create($delivery_booking_detail);
        }
        $order= array('is_activated'=>'11');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/deliverybooking')->with('success','Delivery Booking Submitted Successfully');
    }
    //add trucking company
    public function addtrucking(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $trucking = new Trucking_company();
            $trucking->company_name = $post['company_name'];
            $trucking->save();
        }
    }
    // add terminal
    public function addterminal(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $terminal = new CFS_terminal();
            $terminal->terminal_name = $post['terminal_name'];
            $terminal->save();
        }
    }
    //list of orders for warehouse checkin
    public function warehousecheckin()
    {
        $title="Warehouse Check In";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','11')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function warehousecheckinform(Request $request)
    {
        $title="Warehouse Check In Form";
        $order_id=$request->order_id;
        $user=User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders','orders.user_id','=','user_infos.user_id')
            ->where('orders.order_id',$order_id)
            ->get();
        $shipment=Shipments::where('order_id',$order_id)->get();
        $charges=Charges::all();
        $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('orders','orders.order_id','=','shipments.order_id','left')
            ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.order_id',$order_id)
            ->get();
        return view('order.warehouse_checkin')->with(compact('order_id','shipment','shipment_detail','charges','user','title'));
    }
    public function addwarehousecheckinform(Request $request)
    {
        $count=$request->input('count');
        for($cnt=1;$cnt<$count;$cnt++)
        {
            $warehouse_checkin=array('order_id'=>$request->input('order_id'),
                'shipment_id'=>$request->input('shipment_id'.$cnt),
                'cartoon_length'=>$request->input('cartoon_length'.$cnt),
                'cartoon_width'=>$request->input('cartoon_width'.$cnt),
                'cartoon_weight'=>$request->input('cartoon_weight'.$cnt),
                'cartoon_height'=>$request->input('cartoon_height'.$cnt),
                'no_of_cartoon'=>$request->input('no_of_cartoon'.$cnt),
                'unit_per_cartoon'=>$request->input('unit_per_cartoon'.$cnt),
                'cartoon_condition'=>$request->input('cartoon_condition'.$cnt),
                'location'=>$request->input('location'.$cnt)
            );
           $warehouse_checkin_detail=Warehouse_checkin::create($warehouse_checkin);
            if ($request->hasFile('images'.$cnt)) {
                $destinationPath = public_path() . '/uploads/warehouse';
                $images=$request->file('images'.$cnt);
                foreach($images as $image)
                {
                    $file = $request->input('order_id') . '_' .$request->input('shipment_id'.$cnt) . '_' .$cnt.'_' . 'warehouse' . '.' . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $file);
                    $warehouse_checkin_image=array('warehouse_checkin_id'=>$warehouse_checkin_detail->id,
                                                    'images'=>$file
                                                   );
                    Warehouse_checkin_image::create($warehouse_checkin_image);
                }
            }
        }
        $order= array('is_activated'=>'12');
        Order::where('order_id',$request->input('order_id'))->update($order);
        return redirect('order/warehousecheckin')->with('success','Warehouse Checkin Form Submitted Successfully');
    }
    public function adminreview()
    {
        $title="Warehouse Check In Review";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','12')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function downloadwarehouseimages(Request $request)
    {
        $id=$request->id;
        $image=Warehouse_checkin_image::where('id',$id)->get();
        $images=isset($image[0]->images)?$image[0]->images:'';
        $file= public_path(). "/uploads/warehouse/".$images;
        $headers = array('Content-Type: application/pdf',
        );
        return response()->download($file,$images, $headers);
    }
    public function warehousecheckinreview(Request $request)
    {
        if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::selectRaw('warehouse_checkins.*')
                ->join('warehouse_checkins','warehouse_checkins.shipment_id','=','shipments.shipment_id')
                ->where('shipments.order_id',$order_id)->get();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $warehouse_images=Warehouse_checkin_image::where('status','0')->get();
            return view('order/reviewarehousecheckin')->with(compact('shipment','shipment_detail','order_id','warehouse_images'));
        }
    }
    // create shipment plan and shipments
    public function createshipments(Request $request)
    {

            $order_id=$request->order_id;
            $shipment=Order::selectRaw('orders.order_id,orders.user_id,shipments.*')
                ->join('shipments','shipments.order_id','=','orders.order_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $user_id=isset($shipment)?$shipment[0]->user_id:'';
            $user_details = User_info::where('user_id',$user_id)->get();
        $results = Amazon_marketplace::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken, amazon_marketplaces.market_place_id")
            ->join('customer_amazon_details', 'customer_amazon_details.mws_market_place_id', '=', 'amazon_marketplaces.id')
            ->get();

            $UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
            $UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
            $UserCredentials['marketplace'] = $results[0]->market_place_id ? $results[0]->market_place_id : '';
                //$UserCredentials['mws_authtoken']='test';
            //$UserCredentials['mws_seller_id']='A2YCP5D68N9M7J';
            $fromaddress= new \FBAInboundServiceMWS_Model_Address();
            $fromaddress->setName($user_details[0]->company_name);
            $fromaddress->setAddressLine1($user_details[0]->company_address);
            $fromaddress->setCountryCode($user_details[0]->company_country);
            $fromaddress->setStateOrProvinceCode($user_details[0]->company_state);
            $fromaddress->setCity($user_details[0]->company_city);
            $fromaddress->setPostalCode($user_details[0]->company_zipcode);
            $service = $this->getReportsClient();
            $ship_request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest();
            $ship_request->setSellerId($UserCredentials['mws_seller_id']);
            $ship_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
            $ship_request->setShipFromAddress($fromaddress);
            foreach ($shipment as $shipments)
            {
                $shipment_detail=Shipment_detail::selectRaw('shipment_details.total, amazon_inventories.sellerSKU')
                    ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id')
                    ->where('shipment_details.shipment_id',$shipments->shipment_id)->get();
                $item=array();
                foreach ($shipment_detail as $shipment_details)
                {
                    $data =array('SellerSKU'=>$shipment_details->sellerSKU,'Quantity'=>$shipment_details->total);
                    $item[] = new \FBAInboundServiceMWS_Model_InboundShipmentPlanItem($data);
                }
                $itemlist = new \FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList();
                $itemlist->setmember($item);
                $ship_request->setInboundShipmentPlanRequestItems($itemlist);

                $arr_response =$this->invokeCreateInboundShipmentPlan($service, $ship_request);
                $shipment_id=$shipments->shipment_id;
                //create shipments api of perticular shipmentplan
                $shipment_service = $this->getReportsClient();
                $shipment_request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentRequest();
                $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
                $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                $shipment_header= new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                $shipment_header->setShipmentName("SHIPMENT_NAME");
                $shipment_header->setShipFromAddress($fromaddress);
                //response of shipment plan and insert data in amazon destination
                foreach ($arr_response as $new_response) {
                    foreach ($new_response->InboundShipmentPlans as $planresult) {
                        foreach ($planresult->member as $member) {
                            $api_shipment_id = $member->ShipmentId;
                            $destination_name = $member->DestinationFulfillmentCenterId;
                            foreach ($member->ShipToAddress as $address)
                            {
                                $address_name=$address->Name;
                                $addressline1=$address->AddressLine1;
                                $city=$address->City;
                                $state=$address->StateOrProvinceCode;
                                $country=$address->CountryCode;
                                $postal=$address->PostalCode;
                            }
                            $preptype=$member->LabelPrepType;
                            foreach ($member->EstimatedBoxContentsFee as $fee)
                            {
                                $total_unit=$fee->TotalUnits;
                                foreach ($fee->FeePerUnit as $unit)
                                {
                                    $unit_currency=$unit->CurrencyCode;
                                    $unit_value=$unit->Value;
                                }
                                foreach ($fee->TotalFee as $total)
                                {
                                    $total_currency=$total->CurrencyCode;
                                    $total_value=$total->Value;
                                }
                            }
                            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
                            $shipment_request->setInboundShipmentHeader($shipment_header);
                            $shipment_request->setShipmentId($api_shipment_id);
                            $shipment_item=array();
                            foreach ($member->Items as $item)
                            {
                                foreach ($item->member as $sub_member)
                                {
                                    $amazon_destination = array('destination_name'=>$destination_name,
                                        'shipment_id'=>$shipment_id,
                                        'api_shipment_id'=>$api_shipment_id,
                                        'sellerSKU'=>$sub_member->SellerSKU,
                                        'fulfillment_network_SKU'=>$sub_member->FulfillmentNetworkSKU,
                                        'qty'=>$sub_member->Quantity,
                                        'ship_to_address_name'=>$address_name,
                                        'ship_to_address_line1'=>$addressline1,
                                        'ship_to_city'=>$city,
                                        'ship_to_state_code'=>$state,
                                        'ship_to_country_code'=>$country,
                                        'ship_to_postal_code'=>$postal,
                                        'label_prep_type'=>$preptype,
                                        'total_units'=>$total_unit,
                                        'fee_per_unit_currency_code'=>$unit_currency,
                                        'fee_per_unit_value'=>$unit_value,
                                        'total_fee_value'=>$total_value
                                    );
                                    Amazon_destination::create($amazon_destination);
                                    $item_array= array('SellerSKU'=>$sub_member->SellerSKU, 'QuantityShipped'=>$sub_member->Quantity, 'FulfillmentNetworkSKU'=>$sub_member->FulfillmentNetworkSKU);
                                    $shipment_item[]= new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                                }
                            }
                            $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                            $api_shipment_detail->setmember($shipment_item);
                            $shipment_request->setInboundShipmentItems($api_shipment_detail);
                            $this->invokeCreateInboundShipment($shipment_service, $shipment_request);
                        }
                    }
                }
            }
            $plan=array('shipmentplan'=>'1','is_activated'=>'13');
            Order::where('order_id',$order_id)->update($plan);
        $shipment_ids=Amazon_destination::selectRaw('amazon_destinations.api_shipment_id, warehouse_checkins.no_of_cartoon')
            ->join('shipments','shipments.shipment_id','=','amazon_destinations.shipment_id')
            ->join('warehouse_checkins','warehouse_checkins.shipment_id','=','shipments.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->groupby('amazon_destinations.api_shipment_id')
            ->get();
        $product_ids=Amazon_destination::selectRaw('amazon_destinations.*, warehouse_checkins.no_of_cartoon')
            ->join('shipments','shipments.shipment_id','=','amazon_destinations.shipment_id')
            ->join('warehouse_checkins','warehouse_checkins.shipment_id','=','shipments.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->distinct('amazon_destinations.api_shipment_id')
            ->get();
        $cartoon_id=1;
        $devAccount = Dev_account::first();
        foreach ($shipment_ids as $new_shipment_ids)
        {
            $feed = '<?xml version="1.0" encoding="UTF-8"?>'.
                '<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">'.
                '<Header>'.
                '<DocumentVersion>1.01</DocumentVersion>'.
                '<MerchantIdentifier>'.$UserCredentials["mws_seller_id"].'</MerchantIdentifier>'.
                '</Header>'.
                '<MessageType>CartonContentsRequest</MessageType>'.
                '<Message>'.
                '<MessageID>1</MessageID>'.
                '<CartonContentsRequest>'.
                '<ShipmentId>'.$new_shipment_ids->api_shipment_id.'</ShipmentId>'.
                '<NumCartons>'.$new_shipment_ids->no_of_cartoon.'</NumCartons>'.
                '<Carton>'.
                '<CartonId>'.$cartoon_id.'</CartonId>';
            foreach ($product_ids as $new_product_ids) {
                if ($new_shipment_ids->api_shipment_id == $new_product_ids->api_shipment_id) {
                    $feed .= '<Item>' .
                        '<SKU>' . $new_product_ids->sellerSKU . '</SKU>' .
                        '<QuantityShipped>' . $new_product_ids->qty . '</QuantityShipped>' .
                        '<QuantityInCase>' . $new_product_ids->qty . '</QuantityInCase>' .
                        '</Item>';
                }
            }
            $feed.='</Carton>'.
                '</CartonContentsRequest>'.
                '</Message>'.
                '</AmazonEnvelope>';

            $param = array();
            $param['AWSAccessKeyId'] = $devAccount->access_key;
            $param['MarketplaceId.Id.1'] =$UserCredentials['marketplace'];
            $param['MWSAuthToken'] = $UserCredentials['mws_authtoken']; //MWS Auth Token for this store
            $param['Merchant'] = $UserCredentials['mws_seller_id'];
            $param['Action'] = 'SubmitFeed';
            $param['FeedType'] = '_POST_FBA_INBOUND_CARTON_CONTENTS_';
            $param['SignatureMethod'] = 'HmacSHA256';
            $param['SignatureVersion'] = '2';
            $param['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
            $param['Version'] = '2009-01-01';
            $param['PurgeAndReplace'] = 'false';
            $strUrl = $this->arrToQueryString($param);
            $result = $this->sendQuery($strUrl, $feed, $param);
            $arr = simplexml_load_string($result);
            foreach ($arr as $new_arr)
            {
                foreach ($new_arr->FeedSubmissionInfo as $feedsubmit)
                {
                    $feed_id=$feedsubmit->FeedSubmissionId;
                }
            }
            $feed_list = '<?xml version="1.0" encoding="UTF-8"?>'.
                '<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">'.
                '<Header>'.
                '<DocumentVersion>1.02</DocumentVersion>'.
                '<MerchantIdentifier>'.$UserCredentials["mws_seller_id"].'</MerchantIdentifier>'.
                '</Header>'.
                '<MessageType>ProcessingReport</MessageType>'.
                '<Message>'.
                '<MessageID>1</MessageID>'.
                '<ProcessingReport>'.
                '<DocumentTransactionID>'.$feed_id.'</DocumentTransactionID>'.
                '</ProcessingReport>'.
                '</Message>'.
                '</AmazonEnvelope>';
            $param = array();
            $param['AWSAccessKeyId'] = $devAccount->access_key;
            $param['MarketplaceId.Id.1'] = $UserCredentials['marketplace'];
            $param['MWSAuthToken'] = $UserCredentials['mws_authtoken']; //MWS Auth Token for this store
            $param['Merchant'] = $UserCredentials['mws_seller_id'];
            $param['Action'] = 'GetFeedSubmissionResult';
            $param['SignatureMethod'] = 'HmacSHA256';
            $param['SignatureVersion'] = '2';
            $param['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
            $param['Version'] = '2009-01-01';
            $param['PurgeAndReplace'] = 'false';
            $param['FeedSubmissionId'] = $feed_id;
            $strUrl = $this->arrToQueryString($param);
            $feed_result = $this->sendQuery($strUrl, $feed_list, $param);
            $data= array('cartoon_id'=>$cartoon_id, 'feed_submition_id'=>$feed_id);
            Amazon_destination::where('api_shipment_id',$new_shipment_ids->api_shipment_id)->update($data);
            $cartoon_id++;
        }
        return redirect('order/warehousecheckin')->with('success','Shipment Created Successfully');
    }
    protected function getReportsClient()
    {
        list($access_key, $secret_key, $config) = $this->getKeys();
        return  new \FBAInboundServiceMWS_Client(
            $access_key,
            $secret_key,
            env('APPLICATION_NAME'),
            env('APPLICATION_VERSION'),
            $config
        );
    }
    private function getKeys()
    {
        add_to_path('Libraries');
        $devAccount = Dev_account::first();
        //$accesskey='AKIAJSMUMYFXUPBXYQLA';
        //$secret_key='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';
        return [
           $devAccount->access_key,
           $devAccount->secret_key,
           // $accesskey,
           // $secret_key,
            self::getMWSConfig()
        ];
    }
    public static function getMWSConfig()
    {
        return [
            'ServiceURL' =>"https://mws.amazonservices.com/FulfillmentInboundShipment/2010-10-01" ,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        ];
    }
    function invokeCreateInboundShipmentPlan(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {
            $response = $service->CreateInboundShipmentPlan($request);
            // echo ("Service Response\n");
            // echo ("=============================================================================\n");
            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->saveXML();
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
            return $arr_response = new \SimpleXMLElement($dom->saveXML());
        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
    function invokeCreateInboundShipment(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {
            $response = $service->CreateInboundShipment($request);
            //echo ("Service Response\n");
            //echo ("=============================================================================\n");
            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->saveXML();
            return 1;
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
    function invokeUpdateInboundShipment(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {
            $response = $service->UpdateInboundShipment($request);
            //echo ("Service Response\n");
            //echo ("=============================================================================\n");
            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->saveXML();
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
            return $update_response = new \SimpleXMLElement($dom->saveXML());
        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
    function invokeListInboundShipments(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {
            $response = $service->ListInboundShipments($request);
            //echo("Service Response\n");
            //echo("=============================================================================\n");
            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->saveXML();
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
            return $list_response = new \SimpleXMLElement($dom->saveXML());
        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
    public function arrToQueryString($param)
    {
        $strURL = "";
        $url = array();
        foreach ($param as $key => $val) {
            $key = str_replace("%7E", "~", rawurlencode($key));
            $val = str_replace("%7E", "~", rawurlencode($val));
            $url[] = "{$key}={$val}";
        }
        sort($url);
        $strURL = implode('&', $url);
        return $strURL;
    }
    public function sendQuery($strUrl, $amazon_feed, $param)
    {
        $devAccount = Dev_account::first();
        $strServieURL = preg_replace('#^https?://#', '', 'https://mws.amazonservices.com');
        $strServieURL = str_ireplace("/", "", $strServieURL);
        $sign = 'POST' . "\n";
        $sign .= $strServieURL . "\n";
        $sign .= '/Feeds/' . $param['Version'] . '' . "\n";
        $sign .= $strUrl;
        $signature = hash_hmac("sha256", $sign, $devAccount->secret_key, true);
        $signature = urlencode(base64_encode($signature));
        $httpHeader = array();
        $httpHeader[] = 'Transfer-Encoding: chunked';
        $httpHeader[] = 'Content-Type: application/xml';
        $httpHeader[] = 'Content-MD5: ' . base64_encode(md5($amazon_feed, true));
        $httpHeader[] = 'Expect:';
        $httpHeader[] = 'Accept:';
        $link =  "https://mws.amazonservices.com/Feeds/" . $param['Version'] . "?";
        $link .= $strUrl . "&Signature=" . $signature;
        //  echo $link;
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $amazon_feed);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $response;
    }
    public function orderlabor()
    {
        $title="Order Labor";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','13')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function viewchecklist(Request $request)
    {
        if($request->ajax())
        {
            $user= \Auth::user();
            $user_role=$user->role_id;
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::where('shipments.order_id',$order_id)->get();
            $amazon_destination=Amazon_destination::all();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $order_note=Order_note::where('order_id',$order_id)->get();
            $other_label_detail=Other_label_detail::all();
            return view('order/viewchecklist')->with(compact('shipment','shipment_detail','order_id','amazon_destination','order_note','other_label_detail','user_role'));
        }
    }
    public function getlabel(Request $request)
    {
           $fnsku=$request->fnsku;
           echo $image='<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($fnsku, "C39+",1,50) . '" alt="barcode"   />';
    }
    public function getotherlabel(Request $request)
    {
        echo "This is set";
    }
    public function managerreview()
    {
        $title="Manager Review";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','14')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function prepcomplete(Request $request)
    {
        if($request->ajax()) {
            $post = $request->all();
            $shipment_detail_id = $post['shipment_detail_id'];
            $data=array('prep_complete'=>'1');
            Shipment_detail::where('shipment_detail_id',$shipment_detail_id)->update($data);
            return 1;
        }
    }
    public function reviewwork(Request $request)
    {
        if($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment=Shipments::where('shipments.order_id',$order_id)->get();
            $amazon_destination=Amazon_destination::all();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $order_note=Order_note::where('order_id',$order_id)->get();
            $other_label_detail=Other_label_detail::all();
            return view('order/review_work')->with(compact('shipment','shipment_detail','order_id','amazon_destination','order_note','other_label_detail'));
        }
    }
    public function completeshipment()
    {
        $title="Complete Review";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','15')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function shippinglabel(Request $request)
    {
        if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::where('shipments.order_id',$order_id)->get();
            $amazon_destination=Amazon_destination::all();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipments.shipping_label, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $order_note=Order_note::where('order_id',$order_id)->get();
            $other_label_detail=Other_label_detail::all();
            return view('order/shipping_label')->with(compact('shipment','shipment_detail','order_id','amazon_destination','order_note','other_label_detail'));
        }
    }
    public function printshippinglabel(Request $request)
    {
        $shipment_id=$request->shipment_id;
        $UserCredentials['mws_authtoken']='test';
        $UserCredentials['mws_seller_id']='A2YCP5D68N9M7J';
        $service = $this->getReportsClient();
        $shipping_request = new \FBAInboundServiceMWS_Model_GetUniquePackageLabelsRequest();
        $shipping_request->setSellerId($UserCredentials['mws_seller_id']);
        $shipping_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
        $shipment_ids=Amazon_destination::selectRaw('amazon_destinations.api_shipment_id, amazon_destinations.feed_submition_id, amazon_destinations.cartoon_id, warehouse_checkins.no_of_cartoon')
                                          ->join('shipments','shipments.shipment_id','=','amazon_destinations.shipment_id')
                                          ->join('warehouse_checkins','warehouse_checkins.shipment_id','=','shipments.shipment_id')
                                          ->where('shipments.shipment_id',$shipment_id)
                                          ->groupby('amazon_destinations.api_shipment_id')
                                          ->get();
        foreach ($shipment_ids as $new_shipment_ids)
        {
            $shipping_request->setShipmentId($new_shipment_ids->api_shipment_id);
            $shipping_request->setPageType('PackageLabel_Letter_2');
                $label_content=new \FBAInboundServiceMWS_Model_PackageIdentifiers();
                $label_content->setmember($new_shipment_ids->cartoon_id);
                $shipping_request->setPackageLabelsToPrint($label_content);
                $response=$this->invokeGetUniquePackageLabels($service, $shipping_request);
                foreach ($response->GetUniquePackageLabelsResult as $packagelabel)
                {
                    foreach ($packagelabel->TransportDocument as $trasport_document)
                    {
                        $pdf_file=$trasport_document->PdfDocument;
                    }
                }
            $data= array("shipping_label"=>"1");
            Shipments::where('shipment_id',$shipment_id)->update($data);
                $zipStr = $pdf_file;
                header('Content-Type: application/zip');
                header('Content-disposition: filename="shipping_label.zip"');
                $out = base64_decode($pdf_file);
                print($out);
                exit;

        }

    }
    function invokeGetUniquePackageLabels(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {

            $response = $service->GetUniquePackageLabels($request);
           // echo ("Service Response\n");
            //echo ("=============================================================================\n");
            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->saveXML();
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
            return $arr_response = new \SimpleXMLElement($dom->saveXML());

        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
    public function verifylabel(Request $request)
    {
        if($request->ajax())
        {
            $post = $request->all();
            $shipment_id=$post['shipment_id'];
            $status=$post['status'];
            $data=array('shipping_label'=>$status);
            Shipments::where('shipment_id',$shipment_id)->update($data);
            return $status;
        }
    }
    public function adminshipmentreview()
    {
        $title="Shipment Review";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','16')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function shipmentreview(Request $request)
    {
        if($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment=Shipments::where('shipments.order_id',$order_id)->get();
            $amazon_destination=Amazon_destination::all();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipping_label, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
                ->join('orders','orders.order_id','=','shipments.order_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id',$order_id)
                ->get();
            $order_note=Order_note::where('order_id',$order_id)->get();
            $other_label_detail=Other_label_detail::all();
            return view('order/admin_shipment_review')->with(compact('shipment','shipment_detail','order_id','amazon_destination','order_note','other_label_detail'));
        }
    }
    public function verifystatus(Request $request)
    {
        if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $data=array('verify_status'=>'1');
            Order::where('order_id',$order_id)->update($data);
        }

    }
    //list orders for sales person
    public function orderlist()
    {
        $title="Orders";
        $user= \Auth::user();
        $user_role=$user->role_id;
        $orders = Order::where('orders.is_activated','<>','0')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed','Pending For Approval','Approve Inspection Report','Shipping Quote','Approve shipping Quote','Shipping Invoice','Upload Shipper Bill','Approve Bill By Logistic','Shipper Pre Alert','Customer Clearance','Delivery Booking','Warehouse Check In','Review Warehouse','Work Order Labor Complete','Approve Completed Work','Order Complete','Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders','orderStatus','user_role','title'));
    }
    public function customers()
    {
        $user_role= \Auth::user();
        $user_role_id=$user_role->role_id;
        $title="Customer List";
        $user= User::selectRaw('users.*, user_infos.*')
                      ->join('user_infos','users.id','=','user_infos.user_id')
                      ->where('role_id','3')
                      ->get();
       return view('order.customers_detail')->with(compact('user','title','user_role_id'));
    }
    public function switchuser(Request $request)
    {
       $user= \Auth::user();
       if($request->status=='0') {
           $request->session()->put('old_user', $user->id);
       }
       else
       {
           $request->session()->forget('old_user');
       }
        \Auth::loginUsingId($request->user_id);
        return view('member.home');
    }
    public function addnotes(Request $request)
    {
        $user= \Auth::user();
        $user_role=$user->role_id;
        $notes= array('order_id'=>$request->input('orderid'),
                      'shipping_notes'=>$request->input('shipping_note'),
                      'prep_notes'=>$request->input('prep_note')
                );
        Order_note::create($notes);
        if($user_role==8)
        return redirect('order/adminreview');
        else
        return redirect('order/orderlist');
    }
    public function viewnotes(Request $request)
    {
        if($request->ajax())
        {
            $post = $request->all();
            $notes=Order_note::where('order_id',$post['order_id'])->get();
            echo json_encode($notes);
            exit;
        }
    }
    public function deletenote(Request $request)
    {
        if($request->ajax())
        {
            $post = $request->all();
            Order_note::where('id',$post['note_id'])->delete();
        }
    }
    public function savenote(Request $request)
    {
        if($request->ajax()) {
            $notes = array('shipping_notes' => $request->input('shipping_note'),
                'prep_notes' => $request->input('prep_note')
            );
            Order_note::where('id',$request->input('note_id'))->update($notes);
        }
    }
    public function getinvoice_detail()
    {
        $title="Invoice Report";
        return view('order.getinvoices')->with(compact('title'));
    }
    public function get_ajax_invoice_detail(Request $request)
    {
        $post=$request->all();
        $start_date=$post['start_date'];
        $end_date=$post['end_date'];
        $doc_number=$post['doc_number'];
        $customer_name=$post['customer_name'];
        if($start_date=='' && $end_date=='' && $doc_number=='' && $customer_name=='') {
            $invoice_details=Invoice_detail::all();
        }
        else if($start_date!='' && $end_date!='' && $doc_number!='' && $customer_name!='')
        {
            $end_date=$end_date."T23:59:59";
            $invoice_details = Invoice_detail::where('created_time', '>=', date('Y-m-d', strtotime($start_date)))->where('created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))->where('docnumber', '=', $doc_number)->Where('customer_ref_name', '=', $customer_name)->get();
        }
        else {
                $end_date=$end_date."T23:59:59";
            if ($start_date != '' && $end_date != '')
                $invoice_details = Invoice_detail::where('created_time', '>=', date('Y-m-d', strtotime($start_date)))->where('created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))->get();
            if ($doc_number != '')
                $invoice_details = Invoice_detail::orWhere('docnumber', '=', $doc_number)->get();
            if ($customer_name != '')
                $invoice_details = Invoice_detail::orWhere('customer_ref_name', '=', $customer_name)->get();
        }

            return Datatables::of($invoice_details)
                ->editColumn('invoice_id', function ($invoice_detail) {
                    return $invoice_detail->invoice_id;
                })
                ->editColumn('synctoken', function ($invoice_detail) {
                    return $invoice_detail->synctoken;
                })
                ->editColumn('created_time', function ($invoice_detail) {
                    return $invoice_detail->created_time;
                })
                ->editColumn('updated_time', function ($invoice_detail) {
                    return $invoice_detail->updated_time;
                })
                ->editColumn('docnumber', function ($invoice_detail) {
                    return $invoice_detail->docnumber;
                })
                ->editColumn('txndate', function ($invoice_detail) {
                    return $invoice_detail->txndate;
                })
                ->editColumn('customer_ref_name', function ($invoice_detail) {
                    return $invoice_detail->customer_ref_name;
                })
                ->editColumn('line1', function ($invoice_detail) {
                    return $invoice_detail->line1;//." ".$invoice_detail->line2." ".$invoice_detail->city." ".$invoice_detail->country." ".$invoice_detail->postalcode;
                })
                ->editColumn('lat', function ($invoice_detail) {
                    return $invoice_detail->lat;
                })
                ->editColumn('due_date', function ($invoice_detail) {
                    return $invoice_detail->due_date;
                })
                ->editColumn('total_amt', function ($invoice_detail) {
                    return $invoice_detail->total_amt;
                })
                ->editColumn('currancy_ref_name', function ($invoice_detail) {
                    return $invoice_detail->currancy_ref_name;
                })
                ->editColumn('total_taxe', function ($invoice_detail) {
                    return $invoice_detail->total_taxe;
                })
                ->make(true);
    }


}