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

class ListserviceController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }
    //For display list service information of particular order
    public function index(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $list_service = Listing_service::all();
        $product = Shipment_detail::selectRaw("photo_list_details.photo_list_detail_id, photo_list_details.standard_photo, photo_list_details.prop_photo, shipments.order_id, listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.grand_total, listing_service_details.listing_service_ids,shipment_details.product_id, shipment_details.shipment_detail_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.product_nick_name, amazon_inventories.product_nick_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments', 'shipment_details.shipment_id', '=', 'shipments.shipment_id')
            ->join('listing_service_details', 'listing_service_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('photo_list_details', 'photo_list_details.listing_service_detail_id', '=', 'listing_service_details.listing_service_detail_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('listservice.list_service')->with(compact('list_service', 'product'));
    }

    //add list services for particular order
    public function update(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service = array();
            $sub_count = $request->input('sub_count' . $cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[] = $request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if (empty($request->input('listing_service_detail_id' . $cnt))) {
                $list_service = array('order_id' => $request->input('order_id'),
                    'product_id' => $request->input('product_id' . $cnt),
                    'listing_service_ids' => implode(',', $service),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'listing_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                $list_service_detail = new Listing_service_detail($list_service);
                $list_service_detail->save();
                foreach ($service as $services) {
                    if ($services == 1) {
                        $photo_detail = array('listing_service_detail_id' => $list_service_detail->listing_service_detail_id,
                            'standard_photo' => $request->input('standard' . $cnt),
                            'prop_photo' => $request->input('prop' . $cnt)
                        );
                        $photo_list_detail = new Photo_list_detail($photo_detail);
                        $photo_list_detail->save();
                    }
                }
            } else {
                $list_service = array(
                    'product_id' => $request->input('product_id' . $cnt),
                    'listing_service_ids' => implode(',', $service),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'listing_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Listing_service_detail::where('listing_service_detail_id', $request->input('listing_service_detail_id' . $cnt))->update($list_service);
                if (empty($request->input('photo_list_detail_id' . $cnt))) {
                    foreach ($service as $services) {
                        if ($services == 1) {
                            $photo_detail = array('listing_service_detail_id' => $request->input('listing_service_detail_id' . $cnt),
                                'standard_photo' => $request->input('standard' . $cnt),
                                'prop_photo' => $request->input('prop' . $cnt)
                            );
                            $photo_list_detail = new Photo_list_detail($photo_detail);
                            $photo_list_detail->save();
                        }
                    }
                } else {
                    foreach ($service as $services) {
                        if ($services == 1) {
                            $photo_detail = array('listing_service_detail_id' => $request->input('listing_service_detail_id' . $cnt),
                                'standard_photo' => $request->input('standard' . $cnt),
                                'prop_photo' => $request->input('prop' . $cnt)
                            );
                            Photo_list_detail::where('listing_service_detail_id', $request->input('listing_service_detail_id' . $cnt))->update($photo_detail);
                        }
                    }
                }
            }
        }
        $order_detail = array('steps' => '6');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('outboundshipping')->with('Success', 'Listing service Information Added Successfully');
    }

    //remove particular photo details of particular order
    public function removephotolabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            if ($post['service_id'] == '1') {
                $standard_data=array('standard_photo'=>'0');
                Photo_list_detail::where('photo_list_detail_id', $post['photo_list_detail_id'])->update($standard_data);
            }
            else if($post['service_id'] == '2')
            {
                $prop_data=array('prop_photo'=>'0');
                Photo_list_detail::where('photo_list_detail_id', $post['photo_list_detail_id'])->update($prop_data);
            }
        }
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
