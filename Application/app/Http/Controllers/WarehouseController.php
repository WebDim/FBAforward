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


class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }
    public function index()
    {

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
    public function update(Request $request, $id)
    {

    }
    public function destroy($id)
    {
        //
    }
    //list of orders for warehouse checkin
    public function warehousecheckin()
    {
        $title = "Warehouse Check In";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '11')->orderBy('orders.created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->where('orders.is_activated','>=', '11')
            ->where('shipments.is_activated','5')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '11')
            ->where('shipments.is_activated','5')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }
    public function warehousecheckinform(Request $request)
    {
        $title = "Warehouse Check In Form";
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $user = User_info::selectRaw('user_infos.company_name, user_infos.contact_email, orders.order_no')
            ->join('orders', 'orders.user_id', '=', 'user_infos.user_id')
            ->where('orders.order_id', $order_id)
            ->get();
        $shipment = Shipments::where('order_id', $order_id)->where('shipment_id',$shipment_id)->get();
        $charges = Charges::all();
        $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total')
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
            ->where('orders.order_id', $order_id)
            ->where('shipments.shipment_id',$shipment_id)
            ->get();
        return view('warehouse.warehouse_checkin')->with(compact('order_id', 'shipment', 'shipment_detail', 'charges', 'user', 'title'));
    }

    public function addwarehousecheckinform(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $warehouse_checkin = array('order_id' => $request->input('order_id'),
                'shipment_id' => $request->input('shipment_id' . $cnt),
                'cartoon_length' => $request->input('cartoon_length' . $cnt),
                'cartoon_width' => $request->input('cartoon_width' . $cnt),
                'cartoon_weight' => $request->input('cartoon_weight' . $cnt),
                'cartoon_height' => $request->input('cartoon_height' . $cnt),
                'no_of_cartoon' => $request->input('no_of_cartoon' . $cnt),
                'unit_per_cartoon' => $request->input('unit_per_cartoon' . $cnt),
                'cartoon_condition' => $request->input('cartoon_condition' . $cnt),
                'location' => $request->input('location' . $cnt)
            );
            $warehouse_checkin_detail = Warehouse_checkin::create($warehouse_checkin);
            if ($request->hasFile('images' . $cnt)) {
                $destinationPath = public_path() . '/uploads/warehouse';
                $images = $request->file('images' . $cnt);
                foreach ($images as $image) {
                    $file = $request->input('order_id') . '_' . $request->input('shipment_id' . $cnt) . '_' . $cnt . '_' . 'warehouse' . '.' . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $file);
                    $warehouse_checkin_image = array('warehouse_checkin_id' => $warehouse_checkin_detail->id,
                        'images' => $file
                    );
                    Warehouse_checkin_image::create($warehouse_checkin_image);
                }
            }
            $shipment = array('is_activated'=>'6');
            Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
        }
        $orders = Order::where('order_id',$request->input('order_id'))->where('is_activated','>','12')->get();
        if(count($orders)==0) {
            $order = array('is_activated' => '12');
            Order::where('order_id', $request->input('order_id'))->update($order);
        }
        $role = Role::find(8);
        $role->newNotification()
            ->withType('Warehouse check in')
            ->withSubject('You have warehouse check in for review')
            ->withBody('You have warehouse check in for review')
            ->regarding($warehouse_checkin_detail)
            ->deliver();
        $user_detail = User::selectRaw('users.*')
            ->join('orders', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_id', $request->input('order_id'))
            ->get();
        if (count($user_detail) > 0)
            $user = User::find($user_detail[0]->id);
        else
            $user = '';
        $user->newNotification()
            ->withType('Warehouse check in')
            ->withSubject('You order checkin in FBA warehouse')
            ->withBody('You order checkin  in FBA warehouse')
            ->regarding($warehouse_checkin_detail)
            ->deliver();

        return redirect('warehouse/warehousecheckin')->with('success', 'Warehouse Checkin Form Submitted Successfully');
    }

    public function adminreview()
    {
        $title = "Warehouse Check In Review";
        $user = \Auth::user();
        $user_role = $user->role_id;
       /* $orders = Order::selectRaw('orders.*, sum(order_shipment_quantities.quantity) as qty')
            ->join('order_shipment_quantities','order_shipment_quantities.order_id','=','orders.order_id','left')
            ->where('orders.is_activated', '12')
            ->Orwhere('orders.is_activated', '13')
            ->orderBy('orders.created_at', 'desc')
            ->groupby('orders.order_id')
            ->get();*/
       /*$order_id= Order::selectRaw('orders.order_id, ')
                  ->join('shipments','shipments.order_id','=','orders.order_id')
                  ->join('shipment_details','shipment_details.shipment_id','=','shipments.shipment_id')
                  ->join('order_shipment_quantities','order_shipment_quantities.shipment_id','=','shipments.shipment_id')
                  ->groupby('shipments.order_id')
                  ->get();*/
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->where('orders.is_activated','>=', '12')
            ->where('shipments.is_activated','6')
            ->orwhere('shipments.status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        //sum(order_shipment_quantities.quantity) as qty,
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name, shipments.shipmentplan, sum(order_shipment_quantities.quantity) as qty, order_shipment_quantities.status, shipments.status')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->join('order_shipment_quantities','order_shipment_quantities.order_id','=','orders.order_id','left')
            ->where('orders.is_activated','>=', '12')
            ->where('shipments.is_activated','6')
            ->orwhere('shipments.status','0')
            ->groupby('shipments.shipment_id')
            ->get();

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    public function downloadwarehouseimages(Request $request)
    {
        $id = $request->id;
        $image = Warehouse_checkin_image::where('id', $id)->get();
        $images = isset($image[0]->images) ? $image[0]->images : '';
        $file = public_path() . "/uploads/warehouse/" . $images;
        $headers = array('Content-Type: application/pdf',
        );
        return response()->download($file, $images, $headers);
    }

    public function warehousecheckinreview(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::selectRaw('warehouse_checkins.*')
                ->join('warehouse_checkins', 'warehouse_checkins.shipment_id', '=', 'shipments.shipment_id')
                ->where('shipments.order_id', $order_id)
                ->where('shipments.shipment_id', $shipment_id)
                ->groupby('shipments.shipment_id')
                ->get();
            $shipment_detail = Shipment_detail::selectRaw('shipment_details.shipment_detail_id, orders.order_no, shipments.shipment_id,shipment_details.total, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, order_shipment_quantities.quantity, order_shipment_quantities.status') //, order_shipment_quantities.quantity, order_shipment_quantities.status
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
              ->join('order_shipment_quantities','order_shipment_quantities.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id', $shipment_id)
                ->groupby('shipment_details.shipment_detail_id')
                ->get();
            $order_shipment = Order_shipment_quantity::selectRaw('order_shipment_quantities.shipment_detail_id, order_shipment_quantities.quantity, order_shipment_quantities.status')
                ->join('orders','orders.order_id','=','order_shipment_quantities.order_id')
                ->join('shipment_details','order_shipment_quantities.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('order_shipment_quantities.status','0')
                ->groupby('order_shipment_quantities.shipment_detail_id')
                ->get();
            $order_shipped = Order_shipment_quantity::selectRaw('order_shipment_quantities.shipment_detail_id, sum(order_shipment_quantities.quantity) as quantity, order_shipment_quantities.status')
                ->join('orders','orders.order_id','=','order_shipment_quantities.order_id')
                ->join('shipment_details','order_shipment_quantities.shipment_detail_id','=','shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('order_shipment_quantities.status','1')
                ->groupby('order_shipment_quantities.shipment_detail_id')
                ->get();
            $warehouse_images = Warehouse_checkin_image::where('status', '0')->get();

            return view('warehouse/reviewarehousecheckin')->with(compact('shipment', 'shipment_detail', 'order_id', 'warehouse_images','order_shipment','order_shipped'));
        }
    }

    // create shipment plan and shipments
    public function createshipments(Request $request)
    {
        $order_id = $request->order_id;
        $shipment_id = $request->shipment_id;
        $shipment = Order::selectRaw('orders.order_id,orders.user_id,shipments.*')
            ->join('shipments', 'shipments.order_id', '=', 'orders.order_id')
            ->where('orders.order_id', $order_id)
            ->where('shipments.shipment_id',$shipment_id)
            ->get();
        $user_id = isset($shipment) ? $shipment[0]->user_id : '';
        $user_details = User_info::where('user_id', $user_id)->get();
        $results = Amazon_marketplace::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken, amazon_marketplaces.market_place_id")
            ->join('customer_amazon_details', 'customer_amazon_details.mws_market_place_id', '=', 'amazon_marketplaces.id')
            ->where('customer_amazon_details.user_id', $shipment[0]->user_id)
            ->get();
        //$UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
        //$UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
        $UserCredentials['marketplace'] = $results[0]->market_place_id ? $results[0]->market_place_id : '';
        $UserCredentials['mws_authtoken']='test';
        $UserCredentials['mws_seller_id']='A2YCP5D68N9M7J';
        $fromaddress = new \FBAInboundServiceMWS_Model_Address();
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
        foreach ($shipment as $shipments) {
            $shipment_detail = Shipment_detail::selectRaw('shipment_details.total,shipment_details.shipment_detail_id, order_shipment_quantities.quantity, amazon_inventories.sellerSKU')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
                ->join('order_shipment_quantities', 'order_shipment_quantities.shipment_detail_id', '=', 'shipment_details.shipment_detail_id','left')
                ->where('shipment_details.shipment_id', $shipments->shipment_id)
                ->where('order_shipment_quantities.status','0')
                ->get();
            $item = array();
            $shipment_detail_id=array();
            foreach ($shipment_detail as $shipment_details) {
                $shipment_detail_id[]=$shipment_details->shipment_detail_id;
                if($shipment_details->quantity > 0) {
                    $data = array('SellerSKU' => $shipment_details->sellerSKU, 'Quantity' => $shipment_details->quantity);
                    $item[] = new \FBAInboundServiceMWS_Model_InboundShipmentPlanItem($data);
                }
            }
            if(!empty($item)) {
                $itemlist = new \FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList();
                $itemlist->setmember($item);
                $ship_request->setInboundShipmentPlanRequestItems($itemlist);
                $arr_response = $this->invokeCreateInboundShipmentPlan($service, $ship_request);
                $shipment_id = $shipments->shipment_id;
                //create shipments api of particular shipmentplan
                $shipment_service = $this->getReportsClient();
                $shipment_request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentRequest();
                $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
                $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                $shipment_header = new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                $shipment_header->setShipmentName("SHIPMENT_NAME");
                $shipment_header->setShipFromAddress($fromaddress);
                //response of shipment plan and insert data in amazon destination
                foreach ($arr_response as $new_response) {
                    foreach ($new_response->InboundShipmentPlans as $planresult) {
                        foreach ($planresult->member as $member) {
                            $api_shipment_id = $member->ShipmentId;
                            $destination_name = $member->DestinationFulfillmentCenterId;
                            foreach ($member->ShipToAddress as $address) {
                                $address_name = $address->Name;
                                $addressline1 = $address->AddressLine1;
                                $city = $address->City;
                                $state = $address->StateOrProvinceCode;
                                $country = $address->CountryCode;
                                $postal = $address->PostalCode;
                            }
                            $preptype = $member->LabelPrepType;
                            foreach ($member->EstimatedBoxContentsFee as $fee) {
                                $total_unit = $fee->TotalUnits;
                                foreach ($fee->FeePerUnit as $unit) {
                                    $unit_currency = $unit->CurrencyCode;
                                    $unit_value = $unit->Value;
                                }
                                foreach ($fee->TotalFee as $total) {
                                    $total_currency = $total->CurrencyCode;
                                    $total_value = $total->Value;
                                }
                            }
                            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
                            $shipment_request->setInboundShipmentHeader($shipment_header);
                            $shipment_request->setShipmentId($api_shipment_id);
                            $shipment_item = array();
                            foreach ($member->Items as $item) {
                                foreach ($item->member as $sub_member) {
                                    $amazon_destination = array('destination_name' => $destination_name,
                                        'shipment_id' => $shipment_id,
                                        'api_shipment_id' => $api_shipment_id,
                                        'sellerSKU' => $sub_member->SellerSKU,
                                        'fulfillment_network_SKU' => $sub_member->FulfillmentNetworkSKU,
                                        'qty' => $sub_member->Quantity,
                                        'ship_to_address_name' => $address_name,
                                        'ship_to_address_line1' => $addressline1,
                                        'ship_to_city' => $city,
                                        'ship_to_state_code' => $state,
                                        'ship_to_country_code' => $country,
                                        'ship_to_postal_code' => $postal,
                                        'label_prep_type' => $preptype,
                                        'total_units' => $total_unit,
                                        'fee_per_unit_currency_code' => $unit_currency,
                                        'fee_per_unit_value' => $unit_value,
                                        'total_fee_value' => $total_value
                                    );
                                    Amazon_destination::create($amazon_destination);
                                    $item_array = array('SellerSKU' => $sub_member->SellerSKU, 'QuantityShipped' => $sub_member->Quantity, 'FulfillmentNetworkSKU' => $sub_member->FulfillmentNetworkSKU);
                                    $shipment_item[] = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
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
            $status =array('status'=>'1');
            Order_shipment_quantity::whereIn('shipment_detail_id',$shipment_detail_id)->update($status);
            $shipment_data = array('shipmentplan'=>'1');
            Shipments::where('shipment_id',$shipments->shipment_id)->update($shipment_data);
            /*$quantity_detail = Order_shipment_quantity::selectRaw('sum(order_shipment_quantities.quantity) as qty, shipment_details.total')
                               ->join('shipment_details','shipment_details.shipment_detail_id','=','order_shipment_quantities.shipment_detail_id')
                               ->where('order_shipment_quantities.shipment_id',$shipments->shipment_id)
                               ->where('order_shipment_quantities.status','1')
                               ->groupby('order_shipment_quantities.shipment_id')
                               ->get();

            foreach ($quantity_detail as $quantity_details)
            {
                if($quantity_details->qty == $quantity_details->total)
                {
                    $shipment_data1 = array('status'=>'1');
                    Shipments::where('shipment_id',$shipments->shipment_id)->update($shipment_data1);
                }
            }*/
        }

        $shipment_ids = Amazon_destination::selectRaw('amazon_destinations.api_shipment_id, warehouse_checkins.no_of_cartoon')
            ->join('shipments', 'shipments.shipment_id', '=', 'amazon_destinations.shipment_id')
            ->join('warehouse_checkins', 'warehouse_checkins.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->groupby('amazon_destinations.api_shipment_id')
            ->get();
        $product_ids = Amazon_destination::selectRaw('amazon_destinations.*, warehouse_checkins.no_of_cartoon')
            ->join('shipments', 'shipments.shipment_id', '=', 'amazon_destinations.shipment_id')
            ->join('warehouse_checkins', 'warehouse_checkins.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.order_id', $order_id)
            ->distinct('amazon_destinations.api_shipment_id')
            ->get();
        $cartoon_id = 1;
        //$devAccount = Dev_account::first();
        //$access_key = $devAccount->access_key;
        $access_key='AKIAJSMUMYFXUPBXYQLA';
        foreach ($shipment_ids as $new_shipment_ids) {
            $feed = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">' .
                '<Header>' .
                '<DocumentVersion>1.01</DocumentVersion>' .
                '<MerchantIdentifier>' . $UserCredentials["mws_seller_id"] . '</MerchantIdentifier>' .
                '</Header>' .
                '<MessageType>CartonContentsRequest</MessageType>' .
                '<Message>' .
                '<MessageID>1</MessageID>' .
                '<CartonContentsRequest>' .
                '<ShipmentId>' . $new_shipment_ids->api_shipment_id . '</ShipmentId>' .
                '<NumCartons>' . $new_shipment_ids->no_of_cartoon . '</NumCartons>' .
                '<Carton>' .
                '<CartonId>' . $cartoon_id . '</CartonId>';
            foreach ($product_ids as $new_product_ids) {
                if ($new_shipment_ids->api_shipment_id == $new_product_ids->api_shipment_id) {
                    $feed .= '<Item>' .
                        '<SKU>' . $new_product_ids->sellerSKU . '</SKU>' .
                        '<QuantityShipped>' . $new_product_ids->qty . '</QuantityShipped>' .
                        '<QuantityInCase>' . $new_product_ids->qty . '</QuantityInCase>' .
                        '</Item>';
                }
            }
            $feed .= '</Carton>' .
                '</CartonContentsRequest>' .
                '</Message>' .
                '</AmazonEnvelope>';

            $param = array();
            $param['AWSAccessKeyId'] = $access_key;
            $param['MarketplaceId.Id.1'] = $UserCredentials['marketplace'];
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
            foreach ($arr as $new_arr) {
                foreach ($new_arr->FeedSubmissionInfo as $feedsubmit) {
                    $feed_id = $feedsubmit->FeedSubmissionId;
                }
            }
            $feed_list = '<?xml version="1.0" encoding="UTF-8"?>' .
                '<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">' .
                '<Header>' .
                '<DocumentVersion>1.02</DocumentVersion>' .
                '<MerchantIdentifier>' . $UserCredentials["mws_seller_id"] . '</MerchantIdentifier>' .
                '</Header>' .
                '<MessageType>ProcessingReport</MessageType>' .
                '<Message>' .
                '<MessageID>1</MessageID>' .
                '<ProcessingReport>' .
                '<DocumentTransactionID>' . $feed_id . '</DocumentTransactionID>' .
                '</ProcessingReport>' .
                '</Message>' .
                '</AmazonEnvelope>';
            $param = array();
            $param['AWSAccessKeyId'] = $access_key;
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
            $data = array('cartoon_id' => $cartoon_id, 'feed_submition_id' => $feed_id);
            Amazon_destination::where('api_shipment_id', $new_shipment_ids->api_shipment_id)->update($data);
            $cartoon_id++;
        }
        return redirect('warehouse/adminreview')->with('success', 'Shipment Created Successfully');
    }

    protected function getReportsClient()
    {
        list($access_key, $secret_key, $config) = $this->getKeys();
        return new \FBAInboundServiceMWS_Client(
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
        //$devAccount = Dev_account::first();
        $accesskey='AKIAJSMUMYFXUPBXYQLA';
        $secret_key='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';
        return [
          //  $devAccount->access_key,
          //  $devAccount->secret_key,
             $accesskey,
             $secret_key,
            self::getMWSConfig()
        ];
    }

    public static function getMWSConfig()
    {
        return [
            'ServiceURL' => "https://mws.amazonservices.com/FulfillmentInboundShipment/2010-10-01",
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
        //$secret_key = $devAccount->secret_key;
        $secret_key='Uo3EMqenqoLCyCnhVV7jvOeipJ2qECACcyWJWYzF';
        $strServieURL = preg_replace('#^https?://#', '', 'https://mws.amazonservices.com');
        $strServieURL = str_ireplace("/", "", $strServieURL);
        $sign = 'POST' . "\n";
        $sign .= $strServieURL . "\n";
        $sign .= '/Feeds/' . $param['Version'] . '' . "\n";
        $sign .= $strUrl;
        $signature = hash_hmac("sha256", $sign, $secret_key, true);
        $signature = urlencode(base64_encode($signature));
        $httpHeader = array();
        $httpHeader[] = 'Transfer-Encoding: chunked';
        $httpHeader[] = 'Content-Type: application/xml';
        $httpHeader[] = 'Content-MD5: ' . base64_encode(md5($amazon_feed, true));
        $httpHeader[] = 'Expect:';
        $httpHeader[] = 'Accept:';
        $link = "https://mws.amazonservices.com/Feeds/" . $param['Version'] . "?";
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
        $title = "Order Labor";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '13')->orderBy('orders.created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated')
            ->join('shipments','shipments.order_id','=','orders.order_id','left')
            ->where('orders.is_activated','>=', '13')
            ->where('shipments.is_activated','7')
            ->orwhere('shipments.status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipments.status, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '13')
            ->where('shipments.is_activated','7')
            ->orwhere('shipments.status','0')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    public function viewchecklist(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $user_role = $user->role_id;
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::where('shipments.order_id', $order_id)->where('shipment_id',$shipment_id)->get();
            $amazon_destination = Amazon_destination::all();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $order_note = Order_note::where('order_id', $order_id)->get();
            $other_label_detail = Other_label_detail::all();
            return view('warehouse/viewchecklist')->with(compact('shipment', 'shipment_detail', 'order_id', 'amazon_destination', 'order_note', 'other_label_detail', 'user_role'));
        }
    }

    public function getlabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $fnsku = $post['fnsku'];
            $image = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($fnsku, "C39+", 1, 50) . '" alt="barcode"   />';
            return view('warehouse/barcode')->with(compact('image'));
        }
    }

    public function getotherlabel(Request $request)
    {
        $image = "This is set";
        return view('warehouse/barcode')->with(compact('image'));
    }

    public function managerreview()
    {
        $title = "Manager Review";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '14')->orderBy('orders.created_at', 'desc')->get();
        //DB::enableQueryLog();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->where('orders.is_activated','>=', '14')
            ->where('shipments.is_activated','8')
            ->orwhere('shipments.status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipments.status, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '14')
            ->where('shipments.is_activated','8')
            ->orwhere('shipments.status','0')
            ->get();
        //dd(DB::getQueryLog());

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    public function prepcomplete(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
           // $shipment_detail_id = $post['shipment_detail_id'];
           $amazon_destination_id = $post['amazon_destination_id'];
           $data = array('prep_complete' => '1');
            //Shipment_detail::where('shipment_detail_id', $shipment_detail_id)->update($data);
            Amazon_destination::where('amazon_destination_id',$amazon_destination_id)->update($data);
            return 1;
        }
    }

    public function reviewwork(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::where('shipments.order_id', $order_id)->where('shipments.shipment_id',$shipment_id)->get();
            $amazon_destination = Amazon_destination::all();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $order_note = Order_note::where('order_id', $order_id)->get();
            $other_label_detail = Other_label_detail::all();
            return view('warehouse/review_work')->with(compact('shipment', 'shipment_detail', 'order_id', 'amazon_destination', 'order_note', 'other_label_detail'));
        }
    }

    public function completeshipment()
    {
        $title = "Complete Review";
        $user = \Auth::user();
        $user_role = $user->role_id;
        /*$orders = Order::selectRaw('orders.*, count(shipments.shipment_id) as shipment_count')
            ->join('shipments', 'shipments.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '15')
            ->orderBy('orders.created_at', 'desc')
            ->groupby('orders.order_id')
            ->get();*/
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->where('orders.is_activated','>=', '15')
            ->where('shipments.is_activated','9')
            ->Orwhere('shipments.status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id , shipments.shipment_id, shipments.is_activated as activated, shipments.status, shipping_methods.shipping_name')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '15')
            ->where('shipments.is_activated','9')
            ->Orwhere('shipments.status','0')
            ->get();
        $label_count = Shipments::selectRaw('count(shipment_id) as shipment_count, orders.order_id')
            ->join('orders', 'orders.order_id', '=', 'shipments.order_id')
            ->where('shipments.shipping_label', '3')
            ->groupby('orders.order_id')
            ->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title', 'label_count'));
    }

    public function shippinglabel(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::where('shipments.order_id', $order_id)->where('shipments.shipment_id',$shipment_id)->get();
            $amazon_destination = Amazon_destination::all();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipment_id, shipments.shipping_label, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $order_note = Order_note::where('order_id', $order_id)->get();
            $other_label_detail = Other_label_detail::all();
            return view('warehouse/shipping_label')->with(compact('shipment', 'shipment_detail', 'order_id', 'amazon_destination', 'order_note', 'other_label_detail'));
        }
    }

    public function printshippinglabel(Request $request)
    {
        //$shipment_id = $request->shipment_id;
        $amazon_destination_id = $request->amazon_destination_id;
        $shipment_ids = Amazon_destination::selectRaw('amazon_destinations.api_shipment_id, amazon_destinations.feed_submition_id, amazon_destinations.cartoon_id, amazon_destinations.shipment_id, warehouse_checkins.no_of_cartoon')
            ->join('shipments', 'shipments.shipment_id', '=', 'amazon_destinations.shipment_id')
            ->join('warehouse_checkins', 'warehouse_checkins.shipment_id', '=', 'shipments.shipment_id')
            ->where('amazon_destinations.amazon_destination_id', $amazon_destination_id)
            ->groupby('amazon_destinations.api_shipment_id')
            ->get();
        $shipment_id='';
        if(count($shipment_ids)> 0){
            $shipment_id=$shipment_ids[0]->shipment_id;
        }
        $user_detail = Shipments::where('shipment_id', $shipment_id)->get();
        $user_id = isset($user_detail[0]->user_id) ? $user_detail[0]->user_id : '';
        $results = Amazon_marketplace::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken, amazon_marketplaces.market_place_id")
            ->join('customer_amazon_details', 'customer_amazon_details.mws_market_place_id', '=', 'amazon_marketplaces.id')
            ->where('customer_amazon_details.user_id', $user_id)
            ->get();
        //$UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
        //$UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
        $UserCredentials['mws_authtoken']='test';
        $UserCredentials['mws_seller_id']='A2YCP5D68N9M7J';
        $service = $this->getReportsClient();
        $shipping_request = new \FBAInboundServiceMWS_Model_GetUniquePackageLabelsRequest();
        $shipping_request->setSellerId($UserCredentials['mws_seller_id']);
        $shipping_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
        /*$shipment_ids = Amazon_destination::selectRaw('amazon_destinations.api_shipment_id, amazon_destinations.feed_submition_id, amazon_destinations.cartoon_id, warehouse_checkins.no_of_cartoon')
            ->join('shipments', 'shipments.shipment_id', '=', 'amazon_destinations.shipment_id')
            ->join('warehouse_checkins', 'warehouse_checkins.shipment_id', '=', 'shipments.shipment_id')
            ->where('shipments.shipment_id', $shipment_id)
            ->groupby('amazon_destinations.api_shipment_id')
            ->get();*/
        foreach ($shipment_ids as $new_shipment_ids) {
            $shipping_request->setShipmentId($new_shipment_ids->api_shipment_id);
            $shipping_request->setPageType('PackageLabel_Letter_2');
            $label_content = new \FBAInboundServiceMWS_Model_PackageIdentifiers();
            $label_content->setmember($new_shipment_ids->cartoon_id);
            $shipping_request->setPackageLabelsToPrint($label_content);
            $response = $this->invokeGetUniquePackageLabels($service, $shipping_request);
            foreach ($response->GetUniquePackageLabelsResult as $packagelabel) {
                foreach ($packagelabel->TransportDocument as $trasport_document) {
                    $pdf_file = $trasport_document->PdfDocument;
                }
            }
            $data = array("shipping_label" => "1");
            //Shipments::where('shipment_id', $shipment_id)->update($data);
            Amazon_destination::where('amazon_destination_id', $amazon_destination_id)->update($data);
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
        if ($request->ajax()) {
            $post = $request->all();
            //$shipment_id = $post['shipment_id'];
            $amazon_destination_id = $post['amazon_destination_id'];
            $status = $post['status'];
            $data = array('shipping_label' => $status);
            //Shipments::where('shipment_id', $shipment_id)->update($data);
            Amazon_destination::where('amazon_destination_id', $amazon_destination_id)->update($data);
            return $status;
        }
    }

    public function adminshipmentreview()
    {
        $title = "Shipment Review";
        $user = \Auth::user();
        $user_role = $user->role_id;
        //$orders = Order::where('orders.is_activated', '16')->orderBy('orders.created_at', 'desc')->get();
        $orders = Order::selectRaw('orders.order_id, orders.is_activated, orders.created_at, count(shipments.shipment_id) as shipment_count, shipments.is_activated as activated, shipments.status')
            ->join('shipments','shipments.order_id','=','orders.order_id')
            ->where('orders.is_activated','>=', '16')
            ->where('shipments.is_activated','10')
            ->Orwhere('shipments.status','0')
            ->groupby('orders.order_id')
            ->orderBy('orders.created_at', 'desc')
            ->get();
        $shipments= Shipments::selectRaw('orders.order_id ,shipments.verify_status, shipments.shipment_id, shipments.is_activated as activated, shipping_methods.shipping_name, shipments.status')
            ->join('orders','orders.order_id','=','shipments.order_id')
            ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id')
            ->where('orders.is_activated','>=', '16')
            ->where('shipments.is_activated','10')
            ->Orwhere('shipments.status','0')
            ->get();

        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('order.ordershipping')->with(compact('orders', 'orderStatus', 'user_role','shipments', 'title'));
    }

    public function shipmentreview(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $shipment = Shipments::where('shipments.order_id', $order_id)->where('shipment_id',$shipment_id)->get();
            $amazon_destination = Amazon_destination::all();
            $shipment_detail = Shipment_detail::selectRaw('orders.order_no, shipments.shipping_label, shipments.shipment_id, shipping_methods.shipping_name, amazon_inventories.product_name, amazon_inventories.product_nick_name, shipment_details.fnsku, prep_details.prep_detail_id, shipment_details.shipment_detail_id, shipment_details.prep_complete')
                ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
                ->join('orders', 'orders.order_id', '=', 'shipments.order_id', 'left')
                ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id')
                ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id')
                ->where('orders.order_id', $order_id)
                ->where('shipments.shipment_id',$shipment_id)
                ->get();
            $order_note = Order_note::where('order_id', $order_id)->get();
            $other_label_detail = Other_label_detail::all();
            return view('warehouse/admin_shipment_review')->with(compact('shipment', 'shipment_detail', 'order_id', 'amazon_destination', 'order_note', 'other_label_detail'));
        }
    }

    public function verifystatus(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            $order_id = $post['order_id'];
            $shipment_id = $post['shipment_id'];
            $data = array('verify_status' => '1');
            //Order::where('order_id', $order_id)->update($data);
            Shipments::where('shipment_id',$shipment_id)->update($data);
            $order_quantities = Order_shipment_quantity::selectRaw('sum(order_shipment_quantities.quantity) as qty')
                ->where('order_shipment_quantities.shipment_id','60')
                ->where('order_shipment_quantities.status','1')
                ->groupby('order_shipment_quantities.shipment_id')
                ->get();
            $shipment_quantities = Shipment_detail::selectRaw('sum(shipment_details.total) as total')
                ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
                ->where('shipment_details.shipment_id','60')
                ->groupby('shipment_details.shipment_id')
                ->get();
            if(isset($order_quantities) && isset($shipment_quantities))
            {
                if($order_quantities[0]->qty == $shipment_quantities[0]->total)
                {
                    $shipment_data = array('status'=>'1');
                    Shipments::where('shipment_id',$shipment_id)->update($shipment_data);
                }
            }
        }
    }

    public function orderhistory()
    {
        $title = "Order LaborHistory";
        $user = \Auth::user();
        $user_role = $user->role_id;
        $arr=array('13','14','15','16','17');
        $orders = Order::whereIn('orders.is_activated', $arr)->orderBy('orders.created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Order Placed', 'Pending For Approval', 'Approve Inspection Report', 'Shipping Quote', 'Approve shipping Quote', 'Shipping Invoice', 'Upload Shipper Bill', 'Approve Bill By Logistic', 'Shipper Pre Alert', 'Customer Clearance', 'Delivery Booking', 'Warehouse Check In', 'Review Warehouse', 'Work Order Labor Complete', 'Approve Completed Work', 'Shipment Complete', 'Order Complete', 'Warehouse Complete');
        return view('warehouse.orderhistory')->with(compact('orders', 'orderStatus', 'user_role', 'title'));
    }

}
