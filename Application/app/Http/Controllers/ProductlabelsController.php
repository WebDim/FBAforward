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
class ProductlabelsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }
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
    public function create()
    {
        //
    }
    public function store(Request $request)
    {

    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }
    public function destroy($id)
    {
        //
    }

}
