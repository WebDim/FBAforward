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
use App\Notifications\Usernotification;
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
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated', array('0', '1', '2', '4'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.index')->with(compact('orders', 'orderStatus', 'title'));
    }

    //list completed orders of particular user
    public function orderhistory()
    {
        $title = "Order History";
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->whereIn('is_activated', array('17'))->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
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
            if ($request->user_id) {
                $user_id = $request->user_id;
            } else {
                $user = \Auth::user();
                $user_id = $user->id;
                $user_role = $user->role_id;
                $id = $request->id;
            }
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
                ->where('shipments.user_id', $user_id)
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
            return view('order.detail_list')->with(compact('shipment_detail', 'payment_detail', 'user_role', 'id', 'title'));
        }
    }

    //change order status of particular order
    public function orderstatus(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $status = array('is_activated' => $post['status']);
            Order::where('order_id', $post['order_id'])->update($status);
            if ($post['status'] == '13') {
                $role = Role::find(10);
                $role->newNotification()
                    ->withType('order labor')
                    ->withSubject('You have work order labor for check')
                    ->withBody('You have work order labor for check')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '14') {
                $role = Role::find(11);
                $role->newNotification()
                    ->withType('manager review')
                    ->withSubject('You have order review for check')
                    ->withBody('You have order review for check')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '15') {
                $role = Role::find(10);
                $role->newNotification()
                    ->withType('complete shipment')
                    ->withSubject('You have order shipments for complete')
                    ->withBody('You have order shipments for complete')
                    ->regarding($status)
                    ->deliver();
            } else if ($post['status'] == '16') {
                $role = Role::find(8);
                $role->newNotification()
                    ->withType('order complete')
                    ->withSubject('You have order for complete')
                    ->withBody('You have order  for complete')
                    ->regarding($status)
                    ->deliver();
            }
        }
    }

    //list Approved orders of All users for warhouse manager
    public function ordershipping()
    {
        $title = 'Ship Order';
        $orders = Order::where('is_activated', '3')->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'title'));
    }

    //list orders of All users which select inspections, uploading inspection report by inspector
    public function inspectionreport()
    {
        $title = "Inspection Report";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::selectRaw('orders.*')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '1')
            ->where('supplier_inspections.is_inspection', '1')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
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
        $details = Order::selectRaw('orders.order_id')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '1')
            ->where('supplier_inspections.is_inspection', '0')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->get();
        $order_ids = array();
        foreach ($details as $detail) {
            $order_ids[] = $detail->order_id;
        }
        if (!empty($order_ids))
            $orders = Order::where('orders.is_activated', '3')->orWhereIn('orders.order_id', $order_ids)->orderBy('orders.created_at', 'desc')->get();
        else
            $orders = Order::where('orders.is_activated', '3')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    // to display shippingquote form
    public function shippingquoteform(Request $request)
    {
        $title = "Shipping Quote Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::where('order_id', $order_id)->get();
        $charges = Charges::all();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('orders.order_id', $order_id)
            ->get();
        return view('order.shippingquote')->with(compact('order_id', 'shipping_method', 'shipment', 'charges', 'shipment_detail', 'user', 'title'));
    }

    // to add shipping quote form details
    public function addshippingquoteform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $shipping_quote = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'shipment_port' => $request->input('shipping_port' . $cnt),
                'shipment_term' => $request->input('shipping_term' . $cnt),
                'shipment_weights' => $request->input('weight' . $cnt),
                'chargable_weights' => $request->input('chargable_weight' . $cnt),
                'cubic_meters' => $request->input('cubic_meter' . $cnt),
                'total_shipping_cost' => $request->input('total_shipping_cost' . $cnt),
                'status' => '0'
            );
            $shipping_quote_detail = Shipping_quote::create($shipping_quote);
            $sub_count = $request->input('sub_count' . $cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input('charges' . $cnt . "_" . $sub_cnt))) {
                    $shipping_charges = array('shipping_id' => $shipping_quote_detail->id,
                        'charges_id' => $request->input('charges' . $cnt . "_" . $sub_cnt)
                    );
                    Shipping_charge::create($shipping_charges);
                }
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
        $shipment = Shipments::selectRaw('shipping_quotes.*')
            ->join('shipping_quotes', 'shipping_quotes.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)->get();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('orders.order_id', $order_id)
            ->distinct('shipment_quotes.shipment_id')
            ->get();
        $charges = Charges::selectRaw('charges.name, charges.price, shipping_quotes.shipment_id')
            ->join('shipping_charges', 'shipping_charges.charges_id', '=', 'charges.id')
            ->join('shipping_quotes', 'shipping_quotes.id', '=', 'shipping_charges.shipping_id')
            ->where('shipping_quotes.order_id', $order_id)
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

    // to approve shipping quote by customer
    public function approveshippingquote(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
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
        $orders = Order::where('orders.is_activated', '6')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    //to display bill of lading form
    public function billofladingform(Request $request)
    {
        $title = "Bill Of Lading Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::where('order_id', $order_id)->get();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
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
            }
        }
        $order = array('is_activated' => '7');
        Order::where('order_id', $request->input('order_id'))->update($order);
        $role = Role::find(6);
        $role->newNotification()
            ->withType('bill lading')
            ->withSubject('You have bill of lading for Approval')
            ->withBody('You have bill of lading for Approval')
            ->regarding($order)
            ->deliver();
        return redirect('order/billoflading')->with('success', 'Bill of Lading Uploaded Successfully');
    }

    // display list of order which need approve for bill of lading by logistics
    public function billofladingapprove()
    {
        $title = "Bill Of Lading";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::where('orders.is_activated', '7')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    //display detail of bill of lading to logistic
    public function viewbilloflading(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment = Shipments::selectRaw('bill_of_ladings.*')
                ->join('bill_of_ladings', 'bill_of_ladings.shipment_id', '=', 'shipments.shipment_id')
                ->where('shipments.order_id', $order_id)->get();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
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
            $ladingbill = array('status' => '1');
            Bill_of_lading::where('order_id', $order_id)->update($ladingbill);
            $data = array('is_activated' => '8');
            Order::where('order_id', $order_id)->update($data);
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
        $orders = Order::where('orders.is_activated', '8')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    //to display pre alert form
    public function prealertform(Request $request)
    {
        $title = "Shipment Pre Alert Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
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
                'ETD_china' => $request->input('ETD_china' . $cnt),
                'ETA_US' => $request->input('ETA_US' . $cnt),
                'delivery_port' => $request->input('delivery_port' . $cnt),
                'vessel' => $request->input('vessel' . $cnt),
                'container' => $request->input('container' . $cnt),
                'status' => '0'
            );
            Prealert_detail::create($prealert_detail);
        }
        $order = array('is_activated' => '9');
        Order::where('order_id', $request->input('order_id'))->update($order);
        $role = Role::find(6);
        $role->newNotification()
            ->withType('custom clearance')
            ->withSubject('You have custom clearance for upload')
            ->withBody('You have custom clearance for upload')
            ->regarding($prealert_detail)
            ->deliver();
        return redirect('order/prealert')->with('success', 'Shipment Pre Alert Submitted Successfully');
    }

    //display list of orders which need custom clearnce by logistics
    public function customclearance()
    {
        $title = "Custom Clearance";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::where('orders.is_activated', '9')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    //to display custom clearance form
    public function customclearanceform(Request $request)
    {
        $title = "Custom Clearance Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
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
            $custom_clearance_detail = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'form_3461' => $form_3461image,
                'form_7501' => $form_7501image,
                'delivery_order' => $delivery_orderimage,
                'custom_duty' => $request->input('custom_duty' . $cnt),
                'terminal_fee' => $request->input('terminal_fee' . $cnt),
                'status' => '0'
            );
            $detail = Custom_clearance::create($custom_clearance_detail);
            for ($sub_cnt = 1; $sub_cnt <= 3; $sub_cnt++) {
                if (!empty($request->input('addition_service' . $cnt . "_" . $sub_cnt))) {
                    $additional_service = array('custom_clearance_id' => $detail->id,
                        'service_id' => $request->input('addition_service' . $cnt . "_" . $sub_cnt)
                    );
                    Additional_service::create($additional_service);
                }
            }
        }
        $order = array('is_activated' => '10');
        Order::where('order_id', $request->input('order_id'))->update($order);
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
        $orders = Order::where('orders.is_activated', '10')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    //to display delivery booking form
    public function deliverybookingform(Request $request)
    {
        $title = "Delivery Booking Form";
        $order_id = $request->order_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::selectRaw('shipments.shipment_id, shipping_methods.shipping_name')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('shipments.order_id', $order_id)
            ->orderby('shipments.shipment_id', 'asc')
            ->get();
        $payment_type = Payment_type::all();
        $trucking_company = Trucking_company::all();
        $cfs_terminal = CFS_terminal::all();
        return view('order.delivery_booking')->with(compact('order_id', 'shipment', 'user', 'payment_type', 'trucking_company', 'cfs_terminal', 'title'));
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
                'status' => '0'
            );
            Delivery_booking::create($delivery_booking_detail);
        }
        $order = array('is_activated' => '11');
        Order::where('order_id', $request->input('order_id'))->update($order);
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


    //list orders for sales person
    public function orderlist()
    {
        $title = "Orders";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order::where('orders.is_activated', '<>', '0')->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

    public function customers()
    {
        $user_role = \Auth::user();
        $user_role_id = $user_role->role_id;
        $title = "Customer List";
        $user = User::selectRaw('users.*, user_infos.*')
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->where('role_id', '3')
            ->get();
        return view('order.customers_detail')->with(compact('user', 'title', 'user_role_id'));
    }

    public function getinvoice_detail()
    {
        $title = "Invoice Report";
        return view('order.getinvoices')->with(compact('title'));
    }

    public function get_ajax_invoice_detail(Request $request)
    {
        $post = $request->all();
        $start_date = $post['start_date'];
        $end_date = $post['end_date'];
        $doc_number = $post['doc_number'];
        $customer_name = $post['customer_name'];
        if ($start_date == '' && $end_date == '' && $doc_number == '' && $customer_name == '') {
            $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                ->get();

        } else if ($start_date != '' && $end_date != '' && $doc_number != '' && $customer_name != '') {
            $end_date = $end_date . "T23:59:59";
            $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                ->where('invoice_details.created_time', '>=', date('Y-m-d', strtotime($start_date)))
                ->where('invoice_details.created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))
                ->where('invoice_details.docnumber', '=', $doc_number)
                ->Where('invoice_details.customer_ref_name', '=', $customer_name)
                ->get();
        } else {
            $end_date = $end_date . "T23:59:59";
            if ($start_date != '' && $end_date != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->where('invoice_details.created_time', '>=', date('Y-m-d', strtotime($start_date)))
                    ->where('invoice_details.created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))
                    ->get();
            if ($doc_number != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->orWhere('invoice_details.docnumber', '=', $doc_number)
                    ->get();
            if ($customer_name != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->orWhere('invoice_details.customer_ref_name', '=', $customer_name)
                    ->get();
        }

        return Datatables::of($invoice_details)
            ->editColumn('invoice_id', function ($invoice_detail) {
                return $invoice_detail->invoice_id;
            })
            ->editColumn('order_no', function ($invoice_detail) {
                if ($invoice_detail->order_id != '')
                    return "ORD_" . $invoice_detail->order_id;
                else
                    return "";
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