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

class OutboundshippingController extends Controller
{
    //
    //For display outbound shipping information of particular order
    public function index(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $outbound_method = Outbound_method::all();
        $shipment = Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, shipments.order_id")
            ->join('shipping_methods', 'shipments.shipping_method_id', '=', 'shipping_methods.shipping_method_id')
            ->join('shipment_details', 'shipment_details.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipments.shipment_id')
            ->get();
        $product = Shipment_detail::selectRaw("shipment_details.shipment_id, outbound_shipping_details.outbound_shipping_detail_id,outbound_shipping_details.outbound_method_id, shipment_details.shipment_detail_id, shipments.order_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total,  amazon_inventories.product_name, amazon_inventories.product_nick_name  ")
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('outbound_shipping_details', 'shipment_details.shipment_detail_id', '=', 'outbound_shipping_details.shipment_detail_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('outboundshipping.outbound_shipping')->with(compact('outbound_method', 'product', 'shipment'));
    }

    // add outbound shipping details of particular order
    public function update(Request $request)
    {
        $ship_count = $request->input('ship_count');
        for ($ship_cnt = 1; $ship_cnt < $ship_count; $ship_cnt++) {
            $count = $request->input('count' . $ship_cnt);
            for ($cnt = 1; $cnt < $count; $cnt++) {
                if (empty($request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))) {
                    $outbound_shipping = array(
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_detail_id" => $request->input('shipment_detail_id' . $ship_cnt . "_" . $cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt),
                        "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt)
                    );
                    $outbound_shipping_detail = new Outbound_shipping_detail($outbound_shipping);
                    $outbound_shipping_detail->save();
                } else {
                    $outbound_shipping = array(
                        "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                        "shipment_detail_id" => $request->input('shipment_detail_id' . $ship_cnt . "_" . $cnt),
                        "order_id" => $request->input('order_id'),
                        "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt),
                        "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt)
                    );
                    Outbound_shipping_detail::where('outbound_shipping_detail_id', $request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt))->update($outbound_shipping);
                }
            }
        }
        $order_detail = array('steps' => '7');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('order/reviewshipment')->with('Success', 'Outbound Shipping Information Added Successfully');
    }
}
