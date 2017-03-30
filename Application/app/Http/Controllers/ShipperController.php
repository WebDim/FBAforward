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

class ShipperController extends Controller
{
    private $IntuitAnywhere;
    private $context;
    private $realm;

    public function __construct()
    {
        $this->middleware(['auth']);
    }
    public function index()
    {

    }
    public function openshipment()
    {
        $title = "Open Shipment";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order:: where('is_activated','>','5')->where('is_activated','<','12')->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('shipment.shipment_detail')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }
    public function closeshipment()
    {
        $title = "Close Shipment";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Order:: where('is_activated','>','12')->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('shipment.shipment_detail')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }
    public function viewrejectquote()
    {
        $title = "Rejected Shipping Quote";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $orders = Shipping_quote::selectRaw('orders.*, shipping_quotes.order_id, shipping_quotes.user_id, shipping_quotes.status')
            ->join('orders','shipping_quotes.order_id','=','orders.order_id')
            ->where('orders.is_activated','>','3')
            ->where('shipping_quotes.status','2')
            ->distinct('shipping_quotes.order_id')
            ->distinct('shipping_quotes.user_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('shipper.shippingquote_detail')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }
    public function viewquote(Request $request)
    {
        $order_id = $request->order_id;
        $user_id = $request->user_id;
        $shipment = Shipments::selectRaw('shipping_quotes.*')
            ->join('shipping_quotes', 'shipping_quotes.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->where('shipping_quotes.user_id',$user_id)
            ->where('shipping_quotes.status','2')
            ->orderby('shipping_quotes.id','desc')
            ->limit(2)
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
            ->where('shipping_quotes.status','2')
            ->get();

        return view('shipper/viewshippingquote')->with(compact('shipment','shipment_detail','charges'));
    }
}