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
class PreinspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }
    //For display pre inspection information of particular order
    public function index(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $supplier = Supplier::selectRaw("supplier_inspections.is_inspection, supplier_inspections.inspection_decription, suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id', 'left')
            ->join('supplier_inspections', 'supplier_details.supplier_detail_id', '=', 'supplier_inspections.supplier_detail_id', 'left')
            ->where('supplier_details.order_id', $order_id)
            ->groupby('suppliers.supplier_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.order_id, supplier_inspections.supplier_inspection_id, supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name, amazon_inventories.product_nick_name, amazon_inventories.product_nick_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->join('supplier_inspections', 'supplier_inspections.supplier_detail_id', '=', 'supplier_details.supplier_detail_id', 'left')
            ->where('supplier_details.order_id', $order_id)
            ->distinct('supplier_inspections.is_inspection')
            ->get();
        return view('preinspection.pre_inspection')->with(compact('product', 'supplier'));
    }

    //add pre inspection information for particular order
    public function update(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_count = $request->input('product_count' . $cnt);
            for ($product_cnt = 1; $product_cnt < $product_count; $product_cnt++) {
                if (empty($request->input('supplier_inspection_id' . $cnt . "_" . $product_cnt))) {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'order_id' => $request->input('order_id'),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc' . $cnt),
                        'supplier_id' => $request->input('supplier_id' . $cnt)
                    );
                    $supplier_inspection = new Supplier_inspection($supplier);
                    $supplier_inspection->save();
                } else {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc' . $cnt),
                        'supplier_id' => $request->input('supplier_id' . $cnt)
                    );
                    Supplier_inspection::where('supplier_inspection_id', $request->input('supplier_inspection_id' . $cnt . "_" . $product_cnt))->update($supplier);
                }
            }
        }
        $order_detail = array('steps' => '3');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('productlabels')->with('Success', 'Pre inspection Information Added Successfully');
    }

}
