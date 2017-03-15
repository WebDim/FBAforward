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

class PrepserviceController extends Controller
{
    //
    //For display prep service information of particular order
    public function index(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $prep_service = Prep_service::all();
        $product = Shipment_detail::selectRaw("other_label_details.other_label_detail_id, other_label_details.label_id, shipments.order_id, prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.grand_total, prep_details.prep_service_ids, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.product_nick_name, amazon_inventories.product_nick_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipments', 'shipment_details.shipment_id', '=', 'shipments.shipment_id', 'left')
            ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('other_label_details', 'other_label_details.prep_detail_id', '=', 'prep_details.prep_detail_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('prepservice.prep_service')->with(compact('prep_service', 'product'));
    }

    //add prep service for particular order
    public function update(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service = array();
            $sub_count = $request->input('sub_count' . $cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[] = $request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if (empty($request->input('prep_detail_id' . $cnt))) {
                $prep_service = array('user_id' => $user->id,
                    'order_id' => $request->input('order_id'),
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
                        $other_label = array('label_id' => $request->input('other_label' . $cnt),
                            'prep_detail_id' => $prep_service_detail->prep_detail_id
                        );
                        $other_label_detail = new Other_label_detail($other_label);
                        $other_label_detail->save();
                    }
                }
            } else {
                $prep_service = array('user_id' => $user->id,
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => implode(',', $service),
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Prep_detail::where('prep_detail_id', $request->input('prep_detail_id' . $cnt))->update($prep_service);
                if (empty($request->input('other_label_detail_id' . $cnt))) {
                    foreach ($service as $services) {
                        if ($services == 2) {
                            $other_label = array('label_id' => $request->input('other_label' . $cnt),
                                'prep_detail_id' => $request->input('prep_detail_id' . $cnt)
                            );
                            $other_label_detail = new Other_label_detail($other_label);
                            $other_label_detail->save();
                        }
                    }
                } else {
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
        $order_detail = array('steps' => '5');
        Order::where('order_id', $request->input('order_id'))->update($order_detail);
        return redirect('listservice')->with('Success', 'Prep Service Information Added Successfully');
    }

    //to remove particular other label detail from order
    public function removeotherlabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            Other_label_detail::where('other_label_detail_id', $post['label_detail_id'])->delete();
        }
    }

}
