<?php

namespace App\Http\Controllers;

use App\Http\Requests;
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

class SupplierController extends Controller
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
    public function index(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $product = Shipment_detail::selectRaw("shipments.order_id, shipment_details.shipment_detail_id,supplier_details.supplier_id,  supplier_details.supplier_detail_id,  shipment_details.product_id, shipment_details.total,  amazon_inventories.product_name, amazon_inventories.product_nick_name, amazon_inventories.product_nick_name  ")
            ->join('supplier_details', 'shipment_details.shipment_detail_id', '=', 'supplier_details.shipment_detail_id', 'left')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        $supplier = Supplier::where('user_id', $user->id)->get();
        return view('supplier.supplier')->with(compact('product', 'supplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if (empty($request->input('supplier_detail_id' . $cnt))) {
                $supplier = array('shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'order_id' => $request->input('order_id'),
                    'supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                $supplier_detail = new Supplier_detail($supplier);
                $supplier_detail->save();
            } else {
                $supplier = array('supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                Supplier_detail::where('supplier_detail_id', $request->input('supplier_detail_id' . $cnt))->update($supplier);
                $supplier_inspection = array('supplier_id' => $request->input('supplier' . $cnt), 'is_inspection' => '0', 'inspection_decription' => '');
                Supplier_inspection::where('supplier_detail_id', $request->input('supplier_detail_id' . $cnt))->update($supplier_inspection);
            }
        }
        $order_detail = array('steps' => '2');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
