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
use App\Debitnote_invoice;
use App\Delivery_booking;
use App\Delivery_destination;
use App\Dev_account;
use App\Inspection_report;
use App\Invoice_detail;
use App\Listing_service;
use App\Listing_service_detail;
use App\Notifications\Usernotification;
use App\Order_note;
use App\Order_shipment_quantity;
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
use App\Role;
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
        $this->middleware(['auth', Amazoncredential::class]);
    }

    //list Inprogress, Order Placed or Pending For Approval orders of particular user
    public function index()
    {
        $title = "Order Management";
        $user = \Auth::user();
        $shipping_quote = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
        ->join('orders','shipping_quotes.order_id','=','orders.order_id')
        ->where('orders.is_activated','4')
        ->where('shipping_quotes.status','0')
        ->where('orders.user_id',$user->id)
        ->distinct('shipping_quotes.order_id')
        ->distinct('shipping_quotes.user_id')
        ->get();
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated', array('0', '1', '2', '4'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.index')->with(compact('orders', 'orderStatus', 'shipping_quote','title'));
    }

    //list completed orders of particular user
    public function orderhistory()
    {
        $title = "Order History";
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated', array('17'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.order_history')->with(compact('orders', 'orderStatus', 'title'));
    }

    // remove particular order
    public function removeorder(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Listing_service_detail::where('order_id', $post['order_id'])->delete();
            Prep_detail::where('order_id', $post['order_id'])->delete();
            Product_labels_detail::where('order_id', $post['order_id'])->delete();
            Supplier_detail::where('order_id', $post['order_id'])->delete();
            Shipments::where('order_id', $post['order_id'])->delete();
            Order::where('order_id', $post['order_id'])->delete();
            return 1;
        }
    }

    //For display review information of particular order
    public function reviewshipment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $shipment = Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, sum(shipment_details.total) as total")
            ->join('shipping_methods', 'shipments.shipping_method_id', '=', 'shipping_methods.shipping_method_id')
            ->join('shipment_details', 'shipments.shipment_id', '=', 'shipment_details.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipment_details.shipment_id')
            ->get();
        $outbound_detail = Outbound_shipping_detail::selectRaw('outbound_shipping_details.qty, amazon_inventories.product_name, amazon_inventories.product_nick_name, outbound_methods.outbound_name')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'outbound_shipping_details.product_ids', 'left')
            ->join('outbound_methods', 'outbound_shipping_details.outbound_method_id', '=', 'outbound_methods.outbound_method_id', 'left')
            ->where('outbound_shipping_details.order_id', $order_id)
            ->get();
        $product_detail = Shipment_detail::selectRaw('shipments.order_id, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.total, prep_details.prep_service_ids')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id')
            ->where('shipments.order_id', $order_id)
            ->get();
        $prep_service = Prep_service::all();
        return view('order.review_shipment')->with(compact('shipment', 'outbound_detail', 'product_detail', 'prep_service'));
    }

    //to display whole information of particular order
    public function orderDetails(Request $request)
    {
        $title = "Order Detail";
        if ($request->order_id) {
            DB::enableQueryLog();
            $shipment_detail = Shipments::selectRaw("shipments.shipment_id,shipments.shipping_method_id,shipping_methods.shipping_name,shipment_details.product_id, shipment_details.fnsku, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total,amazon_inventories.product_name, amazon_inventories.product_nick_name, supplier_details.supplier_detail_id,supplier_details.supplier_id,suppliers.company_name,supplier_inspections.inspection_decription,product_labels_details.product_label_id,product_labels.label_name,prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.prep_service_ids,listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.listing_service_ids,outbound_shipping_details.amazon_destination_id, outbound_shipping_details.outbound_method_id,outbound_methods.outbound_name,amazon_destinations.destination_name")
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id', 'left')
                ->join('shipment_details', 'shipment_details.shipment_id', '=', 'shipments.shipment_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('supplier_details', 'shipment_details.shipment_detail_id', '=', 'supplier_details.shipment_detail_id', 'left')
                ->join('suppliers', 'suppliers.supplier_id', '=', 'supplier_details.supplier_id', 'left')
                ->join('supplier_inspections', 'supplier_inspections.supplier_detail_id', '=', 'supplier_details.supplier_detail_id', 'left')
                ->join('product_labels_details', 'product_labels_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
                ->join('product_labels', 'product_labels.product_label_id', '=', 'product_labels_details.product_label_id', 'left')
                ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
                ->join('listing_service_details', 'listing_service_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
                ->join('outbound_shipping_details', 'outbound_shipping_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
                ->join('outbound_methods', 'outbound_methods.outbound_method_id', '=', 'outbound_shipping_details.outbound_method_id', 'left')
                ->join('amazon_destinations', 'amazon_destinations.amazon_destination_id', '=', 'outbound_shipping_details.amazon_destination_id', 'left')
                ->where('shipments.order_id', $request->order_id)
                ->orderBy('shipments.shipment_id', 'ASC')
                ->get()->toArray();
            foreach ($shipment_detail as $key => $shipment_details) {
                //Fetch Prep services name
                $prep_service_ids = explode(",", $shipment_details['prep_service_ids']);
                $prep_services = Prep_service::selectRaw("service_name")->whereIn('prep_service_id', $prep_service_ids)->get();
                $service_name = array();
                if (count($prep_services) > 0) {
                    foreach ($prep_services as $prep_service) {
                        $service_name[] = $prep_service->service_name;
                    }
                }
                $shipment_detail[$key]['prep_service_name'] = implode($service_name, ",");
                //Fetch Listing services name
                $listing_service_ids = explode(",", $shipment_details['listing_service_ids']);
                $listing_services = Listing_service::selectRaw("service_name")->whereIn('listing_service_id', $listing_service_ids)->get();
                $listing_service_name = array();
                if (count($listing_services) > 0) {
                    foreach ($listing_services as $listing_service) {
                        $listing_service_name[] = $listing_service->service_name;
                    }
                }
                $shipment_detail[$key]['listing_service_name'] = implode($listing_service_name, ",");
            }
            // Payment Info get
            $payment_detail = Payment_detail::selectRaw('payment_details.*,user_credit_cardinfos.credit_card_number,user_credit_cardinfos.credit_card_type,user_credit_cardinfos.credit_card_id,payment_infos.transaction')
                ->join('payment_infos', 'payment_infos.payment_detail_id', '=', 'payment_details.payment_detail_id', 'left')
                ->join('user_credit_cardinfos', 'user_credit_cardinfos.id', '=', 'payment_details.user_credit_cardinfo_id', 'left')
                ->where('order_id', $request->order_id)->first();
            if (count($payment_detail) > 0)
                $payment_detail = $payment_detail->toArray();
            return view('order.detail_list')->with(compact('shipment_detail', 'payment_detail', 'title'));
        }
    }

    //change order status of particular order
    public function orderstatus(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $status=array();
            $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>',$post['status'])->get();
            if(count($orders)==0) {
                $status = array('is_activated' => $post['status']);
                Order::where('order_id', $post['order_id'])->update($status);
            }
            $shipments = Shipments::where('shipment_id',$request->input('shipment_id'))->where('is_activated','>',$post['ship_status'])->get();
            if(count($shipments)==0) {
                $ship_status = array('is_activated' => $post['ship_status']);
                Shipments::where('shipment_id', $post['shipment_id'])->update($ship_status);
            }
            if ($post['status'] == '13') {
                $role = Role::find(10);
                $role->newNotification()
                    ->withType('order labor')
                    ->withSubject('You have work order labor for check')
                    ->withBody('You have work order labor for check')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '14') {
                $order_qty = array('status'=>'2');
                Order_shipment_quantity::where('status','1')->where('shipment_id',$post['shipment_id'])->update($order_qty);
                $role = Role::find(11);
                $role->newNotification()
                    ->withType('manager review')
                    ->withSubject('You have order review for check')
                    ->withBody('You have order review for check')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '15') {
                $order_qty = array('status'=>'3');
                Order_shipment_quantity::where('status','2')->where('shipment_id',$post['shipment_id'])->update($order_qty);
                $role = Role::find(10);
                $role->newNotification()
                    ->withType('complete shipment')
                    ->withSubject('You have order shipments for complete')
                    ->withBody('You have order shipments for complete')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '16') {
                $order_qty = array('status'=>'4');
                Order_shipment_quantity::where('status','3')->where('shipment_id',$post['shipment_id'])->update($order_qty);
                $role = Role::find(8);
                $role->newNotification()
                    ->withType('order complete')
                    ->withSubject('You have order for complete')
                    ->withBody('You have order  for complete')
                    ->regarding($status)
                    ->deliver();
            }
            else if ($post['status'] == '17') {
                $order_qty = array('status' => '5');
                Order_shipment_quantity::where('status', '4')->where('shipment_id', $post['shipment_id'])->update($order_qty);
                $ship_qty = Shipment_detail::selectraw('shipments.shipment_id, sum(shipment_details.total) as total')
                    ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
                    ->where('shipment_details.shipment_id',$post['shipment_id'])
                    ->groupby('shipment_details.shipment_id')
                    ->get();
                $order_qty = Order_shipment_quantity::selectraw('shipments.shipment_id, sum(order_shipment_quantities.quantity) as qty')
                    ->join('shipments','shipments.shipment_id','=','order_shipment_quantities.shipment_id')
                    ->where('order_shipment_quantities.shipment_id',$post['shipment_id'])
                    ->where('order_shipment_quantities.status','5')
                    ->groupby('order_shipment_quantities.shipment_id')
                    ->get();
                if(count($ship_qty)> 0 &&  count($order_qty) > 0)
                {
                    if($ship_qty[0]->total == $order_qty[0]->qty)
                    {
                        $data = array('status'=>'1');
                        Shipments::where('shipment_id',$post['shipment_id'])->update($data);
                    }
                }

            }
        }
    }

    //list Approved orders of All users for warhouse manager
   /* public function ordershipping()
    {
        $title = 'Ship Order';
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::selectRaw('orders.*, user_infos.company_name, user_infos.contact_email')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('is_activated', '3')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        //$orders = Order::where('is_activated', '3')->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus','user_role', 'title'));
    }*/

    //list orders of All users which select inspections, uploading inspection report by inspector
    public function inspectionreport()
    {
        $title = "Inspection Report";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::selectRaw('orders.*, user_infos.company_name, user_infos.contact_email')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('orders.is_activated', '1')
            ->where('supplier_inspections.is_inspection', '1')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    // to upload inspection report
    public function uploadinspectionreport(Request $request)
    {
        $order_id = $request->input('order_id');

        if ($request->hasFile('report')) {
            $destinationPath = public_path() . '/uploads/reports';
            $image = $order_id . '_' . 'inspectionreport' . '.' . $request->file('report')->getClientOriginalExtension();
            $request->file('report')->move($destinationPath, $image);
            $inpection_data = array('order_id' => $order_id,
                'uploaded_file' => $image,
                'status' => '0'
            );
            $report = Inspection_report::create($inpection_data);
            $data = array('is_activated' => '2');
            Order::where('order_id', $order_id)->update($data);
            $user_detail = User::selectRaw('users.*')
                ->join('orders', 'orders.user_id', '=', 'users.id')
                ->where('orders.order_id', $order_id)
                ->get();
            if (count($user_detail) > 0)
                $user = User::find($user_detail[0]->id);
            else
                $user = '';
            $user->newNotification()
                ->withType('report')
                ->withSubject('You have inspection report for approvel')
                ->withBody('You have inspection report for approvel')
                ->regarding($report)
                ->deliver();
            return redirect('order/inspectionreport')->with('success', 'Report successfully uploaded');
        }
    }

    //to download inspection report
    public function downloadreport(Request $request)
    {
        $order_id = $request->order_id;
        $inspection = Inspection_report::where('order_id', $order_id)->get();
        if (!empty($inspection[0]->uploaded_file)) {
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
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $inpection_data = array('status' => '1');
            Inspection_report::where('order_id', $order_id)->update($inpection_data);
            $data = array('is_activated' => '3');
            Order::where('order_id', $order_id)->update($data);
            $role = Role::find(5);
            $role->newNotification()
                ->withType('shipping quote')
                ->withSubject('You have shipping quote for upload')
                ->withBody('You have shipping quote for upload')
                ->regarding($inpection_data)
                ->deliver();
        }
    }

    //list of all orders which needs upload shipping quote by shipper
    public function shippingquote()
    {
        $title = "Shipping Quote";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $user_id = $user->id;

        $details = Order::selectRaw('orders.order_id, count(supplier_inspections.supplier_inspection_id) as count_id')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '1')
            ->where('supplier_inspections.is_inspection', '0')
            ->orderBy('orders.created_at', 'desc')
            ->groupby('supplier_inspections.order_id')
            ->get();
        $counts = Order::selectRaw('orders.order_id, count(supplier_inspections.supplier_inspection_id) as count_id')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '1')
            ->orderBy('orders.created_at', 'desc')
            ->groupby('supplier_inspections.order_id')
            ->get();
        $order_ids = array();
            foreach ($counts as $count) {
                foreach ($details as $detail) {
                if( ($count->order_id==$detail->order_id) && ($count->count_id==$detail->count_id))
                $order_ids[] = $detail->order_id;
            }
        }
        $shipping_id = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('orders.is_activated','4')
            ->where('shipping_quotes.status','0')
            ->distinct('shipping_quotes.order_id')
            ->distinct('shipping_quotes.user_id')
            ->get();
        $shipping_id1 = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('orders.is_activated','4')
            ->where('shipping_quotes.status','2')
            ->where('shipping_quotes.status','<>','1')
            ->distinct('shipping_quotes.order_id')
            ->distinct('shipping_quotes.user_id')
            ->get();
            foreach ($shipping_id as $shipping_ids)
            {
                $order_ids[]=$shipping_ids->order_id;
            }
            foreach ($shipping_id1 as $shipping_ids1)
            {
                $order_ids[]=$shipping_ids1->order_id;
            }

        if (!empty($order_ids))
            $orders = Order::selectRaw('orders.*, user_infos.company_name, user_infos.contact_email')
                             ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
                             ->where('orders.is_activated', '3')
                             ->orWhereIn('orders.order_id', $order_ids)
                             ->orderBy('orders.created_at', 'desc')
                             ->get();
        else
            $orders = Order::selectRaw('orders.*, user_infos.company_name, user_infos.contact_email')
                             ->where('orders.is_activated', '3')
                             ->orderBy('orders.created_at', 'desc')
                             ->get();

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');

        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','user_id','shipping_id','shipping_id1', 'title'));
    }

    // to display shippingquote form
    public function shippingquoteform(Request $request)
    {
        $title = "Shipping Quote Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, user_infos.user_id, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $user_id= !empty($user) ? $user[0]->user_id : '';
        $shipment = Shipments::where('order_id', $order_id)->get();
        $charges = Charges::all();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total, supplier_details.supplier_id')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->join('supplier_details','supplier_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $supplier = Supplier::where('user_id',$user_id)->get();
        return view('order.shippingquote')->with(compact('order_id', 'shipping_method', 'shipment', 'charges', 'shipment_detail', 'user', 'supplier', 'title'));
    }

    // to add shipping quote form details
    public function addshippingquoteform(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $shipping_quote = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'shipment_port' => $request->input('shipping_port' . $cnt),
                'shipment_term' => $request->input('shipping_term' . $cnt),
                'shipment_weights' => $request->input('weight' . $cnt),
                'chargable_weights' => $request->input('chargable_weight' . $cnt),
                'cubic_meters' => $request->input('cubic_meter' . $cnt),
                'no_of_pallets' => $request->input('pallet' . $cnt),
                'total_shipping_cost' => $request->input('total_shipping_cost' . $cnt),
                'status' => '0',
                'user_id'=>$user->id,
            );
            $shipping_quote_detail = Shipping_quote::create($shipping_quote);
            /*$sub_count = $request->input('sub_count' . $cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input('charges' . $cnt . "_" . $sub_cnt))) {
                    $shipping_charges = array('shipping_id' => $shipping_quote_detail->id,
                        'charges_id' => $request->input('charges' . $cnt . "_" . $sub_cnt)
                    );
                    Shipping_charge::create($shipping_charges);
                }
            }*/
            $charges=$request->input('charges'.$cnt);
            foreach ($charges as $charge)
            {
                $shipping_charges = array('shipping_id' => $shipping_quote_detail->id,
                    'charges_id' => $charge
                );
                Shipping_charge::create($shipping_charges);
            }
        }
         $order = array('is_activated' => '4');
        Order::where('order_id', $request->input('order_id'))->update($order);
        $user_detail = User::selectRaw('users.*')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_id', $request->input('order_id'))
            ->get();
        if (count($user_detail) > 0)
            $user = User::find($user_detail[0]->id);
        else
            $user = '';
        $user->newNotification()
            ->withType('shipping quote')
            ->withSubject('You have shipping quote for approvel')
            ->withBody('You have shipping quote for approvel')
            ->regarding($shipping_quote_detail)
            ->deliver();
        return redirect('order/shippingquote')->with('success', 'Shipping Quote Submitted Successfully');
    }

    //to download shipping quote
    public function viewshippingquote(Request $request)
    {
        $order_id = $request->order_id;
        $user_id = $request->user_id;
        if(empty($request->status))
            $status =0;
        else
            $status = $request->status;
        $shipment = Shipments::selectRaw('shipping_quotes.*')
            ->join('shipping_quotes', 'shipping_quotes.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->where('shipping_quotes.user_id',$user_id)
            ->where('shipping_quotes.status',$status)
            ->get();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $charges = Charges::selectRaw('charges.name, charges.price, shipping_quotes.shipment_id')
            ->join('shipping_charges', 'shipping_charges.charges_id', '=', 'charges.id')
            ->join('shipping_quotes', 'shipping_quotes.id', '=', 'shipping_charges.shipping_id')
            ->where('shipping_quotes.order_id', $order_id)
            ->where('shipping_quotes.user_id',$user_id)
            ->where('shipping_quotes.status',$status)
            ->get();
        view()->share('shipment', $shipment);
        view()->share('shipment_detail', $shipment_detail);
        view()->share('charges', $charges);
        $pdf = PDF::loadView('order/viewshippingquote');
        return $pdf->download('viewshippingquote.pdf');
        /*if($request->ajax())
        {
            $post=$request->all();
            $order_id=$post['order_id'];
            $shipment=Shipments::selectRaw('shipping_quotes.*')
                ->join('shipping_quotes','shipping_quotes.shipment_id','=','shipments.shipment_id')
                ->where('shipments.order_id',$order_id)->get();
            $shipment_detail=Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
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

    // to reject shipping quote by customer
    public function rejectshippingquote(Request $request)
    {
            $order_id = $request->input('order_id');
            $user_id = $request->input('user_id');
            $shipping_quotes_data = array('status' => '2','reason'=>$request->input('reason'));
            Shipping_quote::where('order_id', $order_id)->where('user_id',$user_id)->where('status','<>','2')->update($shipping_quotes_data);
            return redirect('order/index')->with('Success','Shipping Quote Rejected Successfully');
    }

    // to approve shipping quote by customer
    public function approveshippingquote(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $user_id = $post['user_id'];
            $shipping_quotes_data = array('status' => '1');
            Shipping_quote::where('order_id', $order_id)->where('user_id',$user_id)->where('status','0')->update($shipping_quotes_data);
            $shipping_quotes_data1 = array('status' => '2');
            Shipping_quote::where('order_id', $order_id)->where('user_id','!=',$user_id)->update($shipping_quotes_data1);
            /*$data = array('is_activated' => '5');
            Order::where('order_id', $order_id)->update($data);*/
            $this->createCustomer($order_id);
            return 1;
        }
    }

    // quickbook for send automated invoice
    public function qboConnect()
    {
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

    public function createCustomer($order_id)
    {
        $user__detail = User::selectRaw('user_infos.*')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('user_infos', 'user_infos.user_id', '=', 'users.id')
            ->where('orders.order_id', $order_id)
            ->get();
        $exist_user_detail = Customer_quickbook_detail::selectRaw('customer_quickbook_details.*')
            ->join('orders', 'customer_quickbook_details.user_id', '=', 'orders.user_id')
            ->where('orders.order_id', $order_id)
            ->get();

        $this->qboConnect();
        $CustomerService = new \QuickBooks_IPP_Service_Customer();
        $Customer = new \QuickBooks_IPP_Object_Customer();
        if (count($exist_user_detail) == 0) {
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
        } else {
            $resp = $exist_user_detail[0]->customer_id;
            $this->addInvoice($resp, $order_id);
        }
    }

    public function addItem($cust_resp, $order_id)
    {
        $product = Amazon_inventory::selectRaw('amazon_inventories.*')
            ->join('shipment_details', 'shipment_details.product_id', '=', 'amazon_inventories.id', 'left')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        if (isset($product)) {
            $ItemService = new \QuickBooks_IPP_Service_Item();
            foreach ($product as $Item) {
                $items = $ItemService->query($this->context, $this->realm, "SELECT * FROM Item WHERE Name = '$Item->product_name'  ORDER BY Metadata.LastUpdatedTime ");
                $resp[] = $this->getId($items[0]->getId());
            }
            $this->addInvoice($resp, $cust_resp, $order_id);
        } else {
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

    public function addInvoice($cust_resp, $order_id)
    {
        $details = Payment_detail::where('order_id', $order_id)->get();
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $Invoice = new \QuickBooks_IPP_Object_Invoice();
        $Invoice->setDocNumber('WEB' . mt_rand(0, 10000));
        $Invoice->setTxnDate('2013-10-11');
        $product = Amazon_inventory::selectRaw('amazon_inventories.*')
            ->join('shipment_details', 'shipment_details.product_id', '=', 'amazon_inventories.id', 'left')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        $service = array('Sea Freight Shipping from China - Dog', 'Training Collars', 'Customs Brokerage Fees', 'U.S. Port Fees', 'Container Delivery Fee', 'Wire Transfer Fee');
        if (isset($service)) {
            $ItemService = new \QuickBooks_IPP_Service_Item();
            foreach ($service as $services) {
                $Line = new \QuickBooks_IPP_Object_Line();
                $Line->setDetailType('SalesItemLineDetail');
                $Line->setAmount($details[0]->total_cost);
                $Line->setDescription('');
                $items = $ItemService->query($this->context, $this->realm, "SELECT * FROM Item WHERE Name = '$services'  ORDER BY Metadata.LastUpdatedTime ");
                if (!empty($items)) {
                    $resp = $this->getId($items[0]->getId());
                } else {
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
        } else {
            exit;
        }
        if ($resp = $InvoiceService->add($this->context, $this->realm, $Invoice)) {
            $resp = $this->getId($resp);
            $invoice = array('invoice_id' => $resp);
            Order::where('order_id', $order_id)->update($invoice);
            $this->invoice_pdf($order_id);
        } else {
            print($InvoiceService->lastError());
        }
    }

    public function invoice_pdf($order_id)
    {
        //$this->qboConnect();
        $Context = $this->context;
        $realm = $this->realm;
        $InvoiceService = new \QuickBooks_IPP_Service_Invoice();
        $invoices = $InvoiceService->query($Context, $realm, "SELECT * FROM Invoice STARTPOSITION 1 MAXRESULTS 1");
        $invoice = reset($invoices);
        $id = substr($invoice->getId(), 2, -1);
        $data = array('is_activated' => '6');
        Order::where('order_id', $order_id)->update($data);
        $role = Role::find(5);
        $role->newNotification()
            ->withType('bill lading')
            ->withSubject('You have bill of lading for upload')
            ->withBody('You have bill of lading for upload')
            ->regarding($data)
            ->deliver();
        header("Content-Disposition: attachment; filename=" . $order_id . "_invoice.pdf");
        header("Content-type: application/x-pdf");
        $dir = public_path() . "/uploads/bills/";
        file_put_contents($dir . $order_id . "_invoice.pdf", $InvoiceService->pdf($Context, $realm, $id));
    }

    public function getId($resp)
    {
        $resp = str_replace('{', '', $resp);
        $resp = str_replace('}', '', $resp);
        $resp = abs($resp);
        return $resp;
    }

    //list of all orders which needs upload bill of lading by shipper
    public function billoflading()
    {
        $title = "Bill of Lading";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, user_infos.company_name, user_infos.contact_email')
                  ->join('shipments','shipments.order_id','=','orders.order_id')
                  ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
                  ->where('orders.is_activated','>=', '6')
                  ->where('shipments.is_activated','0')
                  ->groupby('orders.order_id')
                  ->orderBy('orders.created_at', 'desc')
                  ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
                    ->join('orders','orders.order_id','=','shipments.order_id')
                    ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
                    ->where('orders.is_activated','>=', '6')
                    ->where('shipments.is_activated','0')
                    ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    //to display bill of lading form
    public function billofladingform(Request $request)
    {
        $title = "Bill Of Lading Form";
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $user = User_info::selectRaw('user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::where('order_id', $order_id)->where('shipment_id',$shipment_id)->get();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.shipment_id',$shipment_id)
            ->where('orders.order_id', $order_id)
            ->get();
        return view('order.billoflading')->with(compact('order_id', 'shipment', 'shipment_detail', 'user', 'title'));
    }

    // to add bill of lading details
    public function addbillofladingform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if ($request->hasFile('bill' . $cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $image = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'lading_bill' . '.' . $request->file('bill' . $cnt)->getClientOriginalExtension();
                $request->file('bill' . $cnt)->move($destinationPath, $image);
                $bill_detail = array('order_id' => $request->input('order_id'),
                    'shipment_id' => $request->input('shipment_id' . $cnt),
                    'sbnumber' => $request->input('ref_number' . $cnt),
                    'bill' => $image,
                    'status' => '0'
                );
                Bill_of_lading::create($bill_detail);
                $shipment = array('is_activated'=>'1');
                Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
            }
        }
        $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','7')->get();
        if(count($orders)==0) {
            $order = array('is_activated' => '7');
            Order::where('order_id', $request->input('order_id'))->update($order);
        }
        $role = Role::find(6);
        $role->newNotification()
            ->withType('bill lading')
            ->withSubject('You have bill of lading for Approval')
            ->withBody('You have bill of lading for Approval')
            ->regarding($shipment)
            ->deliver();
        return redirect('order/billoflading')->with('success', 'Bill of Lading Uploaded Successfully');
    }

    // display list of order which need approve for bill of lading by logistics
    public function billofladingapprove()
    {
        $title = "Bill Of Lading";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '7')->orderBy('orders.created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, user_infos.company_name, user_infos.contact_email')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('orders.is_activated','>=', '7')
            ->where('shipments.is_activated','1')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '7')
            ->where('shipments.is_activated','1')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'shipments', 'title'));
    }

    //display detail of bill of lading to logistic
    public function viewbilloflading(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::selectRaw('bill_of_ladings.*')
                ->join('bill_of_ladings', 'bill_of_ladings.shipment_id', '=', 'shipments.shipment_id')
                ->where('shipments.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->where('shipments.shipment_id',$shipment_id)
                ->where('orders.order_id', $order_id)
                ->get();
            return view('order/viewbilloflading')->with(compact('shipment', 'shipment_detail', 'order_id'));
        }
    }

    //to download bill of lading
    public function downloadladingbill(Request $request)
    {
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $ladingbill = Bill_of_lading::where('order_id', $order_id)->where('shipment_id', $shipment_id)->get();
        $bill = isset($ladingbill[0]->bill) ? $ladingbill[0]->bill : '';
        $file = public_path() . "/uploads/bills/" . $bill;
        $headers = array('Content-Type: application/pdf',
        );
        return response()->download($file, $bill, $headers);
    }

    //approve bill of lading by logistics
    public function approvebilloflading(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $ladingbill = array('status' => '1');
            Bill_of_lading::where('order_id', $order_id)->where('shipment_id',$shipment_id)->update($ladingbill);
            $shipment =array('is_activated'=>'2');
            Shipments::where('shipment_id',$shipment_id)->update($shipment);
            $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','8')->get();
            if(count($orders)==0) {
                $data = array('is_activated' => '8');
                Order::where('order_id', $order_id)->update($data);
            }
            $role = Role::find(5);
            $role->newNotification()
                ->withType('shipment pre alert')
                ->withSubject('You have shipment pre alert for upload')
                ->withBody('You have shipment pre alert for upload')
                ->regarding($ladingbill)
                ->deliver();
            //$this->createCustomer($order_id);
        }
    }

    //list of all orders which needs upload shipment pre alert by shipper
    public function prealert()
    {
        $title = "Shipment Pre Alert";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('is_activated', '8')->Orwhere('is_activated', '9')->where('debitnote_status','0')->orderBy('created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, user_infos.company_name, user_infos.contact_email')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('orders.is_activated','>=', '8')
            ->where('shipments.is_activated','2')
            ->Orwhere('shipments.is_activated','3')
            ->where('shipments.debitnote_status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->join('debitnote_invoices','debitnote_invoices.shipment_id','=','shipments.shipment_id','left')
            ->where('orders.is_activated','>=', '8')
            ->where('shipments.is_activated','2')
            ->Orwhere('shipments.is_activated','3')
            ->where('shipments.debitnote_status','0')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'shipments', 'title'));
    }

    //to display pre alert form
    public function prealertform(Request $request)
    {
        $title = "Shipment Pre Alert Form";
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
            ->where('shipments.shipment_id',$shipment_id)
            ->orderby('shipments.shipment_id', 'asc')
            ->get();
        return view('order.prealert')->with(compact('order_id', 'shipment', 'user', 'title'));
    }

    // add prealert form details
    public function addprealertform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $isfimage = '';
            $hblimage = '';
            $mblimage = '';
            if ($request->hasFile('ISF' . $cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $isfimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'ISF' . '.' . $request->file('ISF' . $cnt)->getClientOriginalExtension();
                $request->file('ISF' . $cnt)->move($destinationPath, $isfimage);
            }
            if ($request->hasFile('HBL' . $cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $hblimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'HBL' . '.' . $request->file('HBL' . $cnt)->getClientOriginalExtension();
                $request->file('HBL' . $cnt)->move($destinationPath, $hblimage);
            }
            if ($request->hasFile('MBL' . $cnt)) {
                $destinationPath = public_path() . '/uploads/bills';
                $mblimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'MBL' . '.' . $request->file('MBL' . $cnt)->getClientOriginalExtension();
                $request->file('MBL' . $cnt)->move($destinationPath, $mblimage);
            }
            $prealert_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'ISF' => $isfimage,
                'HBL' => $hblimage,
                'MBL' => $mblimage,
                'ETD_china' => date('Y-m-d', strtotime($request->input('ETD_china' . $cnt))),
                'ETA_US' => date('Y-m-d', strtotime($request->input('ETA_US' . $cnt))),
                'delivery_port' => $request->input('delivery_port' . $cnt),
                'vessel' => $request->input('vessel' . $cnt),
                'container' => $request->input('container' . $cnt),
                'status' => '0'
            );
            Prealert_detail::create($prealert_detail);
            $shipment = array('is_activated'=>'3');
            Shipments::where('shipment_id',$request->input('shipment_id' . $cnt))->update($shipment);
        }

        $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','9')->get();
        if(count($orders)==0) {
            $order = array('is_activated' => '9');
            Order::where('order_id', $request->input('order_id'))->update($order);
        }
        return redirect('order/prealert')->with('success', 'Shipment Pre Alert Submitted Successfully');
    }
    public function adddebitnote(Request $request)
    {
        $order_id = $request->input('id');
        $shipment_id = $request->input('shipment_id');

        if ($request->hasFile('debitnote')) {
            $destinationPath = public_path() . '/uploads/debitnote_invoice';
            $image = $order_id . '_' .$shipment_id.'_'. 'debitnote_invoice' . '.' . $request->file('debitnote')->getClientOriginalExtension();
            $request->file('debitnote')->move($destinationPath, $image);
            $debitnote_data = array('order_id' => $order_id,
                'shipment_id' => $shipment_id,
                'uploaded_file' => $image,
                'status' => '0'
            );
            $report = Debitnote_invoice::create($debitnote_data);
            $data = array('debitnote_status' => '1');
            Shipments::where('shipment_id', $shipment_id)->update($data);
            $role = Role::find(6);
            $role->newNotification()
                ->withType('custom clearance')
                ->withSubject('You have custom clearance for upload')
                ->withBody('You have custom clearance for upload')
                ->regarding($debitnote_data)
                ->deliver();
            return redirect('order/prealert')->with('success', 'Debitnote/Invoice successfully uploaded');
        }
    }

    //display list of orders which need custom clearnce by logistics
    public function customclearance()
    {
        $title = "Customs Clearance";
        $user = \Auth::user();
        $user_role = $user->role_id;
       // $orders = Order::where('is_activated', '9')->where('debitnote_status', '1')->orderBy('created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, user_infos.company_name, user_infos.contact_email')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('orders.is_activated','>=', '9')
            ->where('shipments.is_activated','3')
            ->where('shipments.debitnote_status','1')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '9')
            ->where('shipments.is_activated','3')
            ->where('shipments.debitnote_status','1')
            ->get();

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    //to display custom clearance form
    public function customclearanceform(Request $request)
    {
        $title = "Customs Clearance Form";
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
            ->where('shipments.shipment_id',$shipment_id)
            ->orderby('shipments.shipment_id', 'asc')
            ->get();
        return view('order.customclearance')->with(compact('order_id', 'shipment', 'user', 'title'));
    }

    //  to add custom clearance form detail
    public function addcustomclearanceform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $form_3461image = '';
            $form_7501image = '';
            $delivery_orderimage = '';
            $abi_noteimage='';
            if ($request->hasFile('form_3461' . $cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $form_3461image = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'form_3461' . '.' . $request->file('form_3461' . $cnt)->getClientOriginalExtension();
                $request->file('form_3461' . $cnt)->move($destinationPath, $form_3461image);
            }
            if ($request->hasFile('form_7501' . $cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $form_7501image = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'form_7501' . '.' . $request->file('form_7501' . $cnt)->getClientOriginalExtension();
                $request->file('form_7501' . $cnt)->move($destinationPath, $form_7501image);
            }
            if ($request->hasFile('delivery_order' . $cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $delivery_orderimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'delivery_order' . '.' . $request->file('delivery_order' . $cnt)->getClientOriginalExtension();
                $request->file('delivery_order' . $cnt)->move($destinationPath, $delivery_orderimage);
            }
            if ($request->hasFile('abi_note' . $cnt)) {
                $destinationPath = public_path() . '/uploads/customclearance';
                $abi_noteimage = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . 'abi_note' . '.' . $request->file('abi_note' . $cnt)->getClientOriginalExtension();
                $request->file('abi_note' . $cnt)->move($destinationPath, $abi_noteimage);
            }
            $custom_clearance_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'form_3461' => $form_3461image,
                'form_7501' => $form_7501image,
                'delivery_order' => $delivery_orderimage,
                'abi_note' => $abi_noteimage,
                'custom_duty' => $request->input('custom_duty' . $cnt),
                'terminal_fee' => $request->input('terminal_fee' . $cnt),
                'status' => '0'
            );
            $detail = Custom_clearance::create($custom_clearance_detail);

            $shipment = array('is_activated'=>'4');
                Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
           /* for ($sub_cnt = 1; $sub_cnt <= 3; $sub_cnt++) {
                if (!empty($request->input('addition_service' . $cnt . "_" . $sub_cnt))) {
                    $additional_service = array('custom_clearance_id' => $detail->id,
                        'service_id' => $request->input('addition_service' . $cnt . "_" . $sub_cnt)
                    );
                    Additional_service::create($additional_service);
                }
            }*/
            $services=$request->input('addition_service'.$cnt);
            foreach ($services as $service)
            {
                $additional_service = array('custom_clearance_id' => $detail->id,
                    'service_id' => $service
                );
                Additional_service::create($additional_service);
            }
        }
        $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','10')->get();
        if(count($orders)==0) {
            $order = array('is_activated' => '10');
            Order::where('order_id', $request->input('order_id'))->update($order);
        }
        $role = Role::find(6);
        $role->newNotification()
            ->withType('delivery booking')
            ->withSubject('You have delivery booking for upload')
            ->withBody('You have delivery booking for upload')
            ->regarding($custom_clearance_detail)
            ->deliver();
        return redirect('order/customclearance')->with('success', 'Custom Clearance Submitted Successfully');
    }

    //list of order which need delivery booking
    public function deliverybooking()
    {
        $title = "Delivery Booking";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '10')->orderBy('orders.created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, user_infos.company_name, user_infos.contact_email')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
            ->where('orders.is_activated','>=', '10')
            ->where('shipments.is_activated','4')
            ->where('shipments.debitnote_status','1')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '10')
            ->where('shipments.is_activated','4')
            ->where('shipments.debitnote_status','1')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    //to display delivery booking form
    public function deliverybookingform(Request $request)
    {
        $title = "Delivery Booking Form";
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
            ->where('shipments.shipment_id',$shipment_id)
            ->orderby('shipments.shipment_id', 'asc')
            ->get();
        $payment_type = Payment_type::all();
        $trucking_company = Trucking_company::all();
        $cfs_terminal = CFS_terminal::all();
        $delivery_destination = Delivery_destination::all();
        return view('order.delivery_booking')->with(compact('order_id', 'shipment', 'user', 'payment_type', 'trucking_company', 'cfs_terminal','delivery_destination', 'title'));
    }

    // to add delivery booking form details
    public function adddeliverybookingform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $delivery_booking_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'CFS_terminal' => $request->input('CFS_terminal' . $cnt),
                'trucking_company' => $request->input('trucking_company' . $cnt),
                'warehouse_fee' => $request->input('warehouse_fee' . $cnt),
                'fee_paid' => $request->input('fee_paid' . $cnt),
                'ETA_warehouse' => date('Y-m-d H:i:s', strtotime($request->input('ETA_warehouse' . $cnt))),
                'delivery_destination'=>$request->input('delivery_destination' . $cnt),
                'last_free_day'=>date('Y-m-d H:i:s', strtotime($request->input('last_free_day' . $cnt))),
                'pallet_exchange'=>$request->input('pallet_exchange' . $cnt),
                'status' => '0'
            );
            Delivery_booking::create($delivery_booking_detail);
            $shipment = array('is_activated'=>'5');
            Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
        }
        $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','11')->get();
        if(count($orders)==0) {
            $order = array('is_activated' => '11');
            Order::where('order_id', $request->input('order_id'))->update($order);
        }
        $role = Role::find(10);
        $role->newNotification()
            ->withType('Warehouse check in')
            ->withSubject('You have warehouse check in for upload')
            ->withBody('You have warehouse check in for upload')
            ->regarding($delivery_booking_detail)
            ->deliver();
        return redirect('order/deliverybooking')->with('success', 'Delivery Booking Submitted Successfully');
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
    // add delivery destination
    public function adddestination(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $destination = new Delivery_destination();
            $destination->destination_name = $post['destination_name'];
            $destination->save();
        }
    }

    //list orders for sales person and customer service
    public function orderlist()
    {
        $title = "Orders";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::selectRaw('orders.*, user_infos.company_name, user_infos.contact_email')
                  ->join('user_infos','user_infos.user_id','=','orders.user_id','left')
                  ->orderBy('orders.created_at', 'desc')
                  ->get();
        $pending_quote = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('shipping_quotes.status','0')
            ->groupby('shipping_quotes.order_id')
            ->get();
        $approve_quote = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('shipping_quotes.status','1')
            ->groupby('shipping_quotes.order_id')
            ->get();
        $reject_quote = Shipping_quote::selectRaw('shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('shipping_quotes.status','2')
            ->groupby('shipping_quotes.order_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customs Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title','pending_quote','approve_quote','reject_quote'));
    }

    //list customer for sales person and customer service
    public function customers()
    {
        $user_role = \Auth::user();
        $user_role_id = $user_role->role_id;
        $title = "Customer List";
        return view('order.customers_detail')->with(compact('title', 'user_role_id'));
    }
    public function customers_detail()
    {
        $user_role = \Auth::user();
        $user_role_id = $user_role->role_id;
        $user = User::selectRaw('users.*, user_infos.*')
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where('role_id', '3')
            ->get();
        return Datatables::of($user)
            ->editColumn('company_name', function ($user) {
                return $user->company_name;
            })
            ->editColumn('company_phone', function ($user) {
                return $user->company_phone;
            })
            ->editColumn('company_address', function ($user) {
                return $user->company_address." ".$user->company_address2." ".$user->city;
            })
            ->editColumn('primary_bussiness_type', function ($user) {
                return $user->primary_bussiness_type;
            })
            ->editColumn('contact_fname', function ($user) {
                return $user->contact_fname." ".$user->contact_lname;
            })
            ->editColumn('contact_email', function ($user) {
                return $user->contact_email;
            })
            ->editColumn('contact_phone', function ($user) {
                return $user->contact_phone;
            })
            ->editColumn('secondary_contact_email', function ($user) {
                return $user->secondary_contact_email;
            })
            ->editColumn('account_payable', function ($user) {
                return $user->account_payable;
            })
            ->editColumn('account_email', function ($user) {
                return $user->account_email;
            })
            ->editColumn('account_phone', function ($user) {
                return $user->account_phone;
            })
            ->editColumn('email', function ($user) {
                return $user->email;
            })
            ->editColumn('tax_id_number', function ($user) {
                return $user->tax_id_number;
            })
            ->editColumn('estimate_annual_amazon_revenue', function ($user) {
                return $user->estimate_annual_amazon_revenue;
            })
            ->editColumn('estimate_annual_fba_order', function ($user) {
                return $user->estimate_annual_fba_order;
            })
            ->editColumn('reference_from', function ($user) {
                return $user->reference_from;
            })
            ->editColumn('Action', function ($user) {
                return $editBtn = '<a onclick="storeuser('.$user->user_id.')"  title="Switch User">Switch User</a>';
            })
            ->make(true);
    }


}