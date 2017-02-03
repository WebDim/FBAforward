<?php
namespace App\Http\Controllers;
use App\Amazon_destination;
use App\Amazon_inventory;
use App\Customer_amazon_detail;
use App\Dev_account;
use App\Listing_service;
use App\Listing_service_detail;
use App\Outbound_method;
use App\Payment_info;
use App\Prep_detail;
use App\Prep_service;
use App\Product_labels;
use App\Supplier_detail;
use App\Shipping_method;
use App\Shipment_detail;
use App\Supplier;
use App\Supplier_inspection;
use App\Product_labels_detail;
use App\Shipments;
use App\Order;
use App\User_credit_cardinfo;
use App\Addresses;
use App\Http\Middleware\Amazoncredential;
use App\Outbound_Shipping_detail;
use App\Payment_detail;
use App\User_info;
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

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth',Amazoncredential::class]);
    }
   //list all order of perticular user
    public function index()
    {
        $user = \Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $orderStatus = array('In Progress', 'Completed');
        return view('order.index')->with(compact('orders','orderStatus'));
    }
    // remove perticular order
    public function removeorder(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
            //update shipments with 0 qty when whole order remove
            $shipment= Shipments::where('order_id',$post['order_id'])->get();
            $shipment_id=array();
            foreach ($shipment as $shipments)
            {
                $shipment_id[]=$shipments->shipment_id;
            }
            $destinations = Amazon_destination::whereIn('shipment_id',$shipment_id)->get();
            $user = \Auth::user();
            $user_details = User_info::where('user_id',$user->id)->get();
            $results = Customer_amazon_detail::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken")
                ->where('user_id',$user->id)
                ->get();
            $UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
            $UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
            $fromaddress= new \FBAInboundServiceMWS_Model_Address();
            $fromaddress->setName($user_details[0]->company_name);
            $fromaddress->setAddressLine1($user_details[0]->company_address);
            $fromaddress->setCountryCode($user_details[0]->company_country);
            $fromaddress->setStateOrProvinceCode($user_details[0]->company_state);
            $fromaddress->setCity($user_details[0]->company_city);
            $fromaddress->setPostalCode($user_details[0]->company_zipcode);
            $update_service = $this->getReportsClient();
            $shipment_request = new \FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest();
            $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
            $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
            foreach ($destinations as $remove_destination) {
                $shipment_header = new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                $shipment_header->setShipmentName("SHIPMENT_NAME");
                $shipment_header->setShipFromAddress($fromaddress);
                $shipment_header->setDestinationFulfillmentCenterId($remove_destination->destination_name);
                $shipment_request->setInboundShipmentHeader($shipment_header);
                $shipment_request->setShipmentId($remove_destination->api_shipment_id);
                $item_array=array();
                $item_array = array('SellerSKU' => isset($remove_destination->sellerSKU) ? $remove_destination->sellerSKU : '', 'QuantityShipped' => '0');
                $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                $api_shipment_detail->setmember($shipment_item);
                $shipment_request->setInboundShipmentItems($api_shipment_detail);
                $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
            }

            Amazon_destination::whereIn('shipment_id',$shipment_id)->delete();
            Listing_service_detail::where('order_id',$post['order_id'])->delete();
            Prep_detail::where('order_id',$post['order_id'])->delete();
            Product_labels_detail::where('order_id',$post['order_id'])->delete();
            Supplier_detail::where('order_id',$post['order_id'])->delete();
            Shipments::where('order_id',$post['order_id'])->delete();
            Order::where('order_id',$post['order_id'])->delete();
            return 1;
        }
    }
    // For display shipment view
    public function shipment(Request $request)
    {
        //Remove session
        $request->session()->forget('order_id');
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment=array();
        return view('order.shipment')->with(compact('shipping_method','product','shipment','orders'));
    }
    // For display update shipment view
    public function updateshipment(Request $request)
    {
         if(!empty($request->order_id)){
            $request->session()->put('order_id', $request->order_id);
            $steps=Order::where('order_id',$request->order_id)->get();
            if($steps[0]->steps==2)
                return redirect('order/supplierdetail');
            else if ($steps[0]->steps==3)
                return redirect('order/preinspection');
            else if ($steps[0]->steps==4)
                return redirect('order/productlabels');
            else if ($steps[0]->steps==5)
                return redirect('order/prepservice');
            else if ($steps[0]->steps==6)
                return redirect('order/listservice');
            else if ($steps[0]->steps==7)
                return redirect('order/outbondshipping');
            else if ($steps[0]->steps==8)
                return redirect('order/reviewshipment');
            else if ($steps[0]->steps==9)
                return redirect('order/payment');
         }
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment= Shipments::where('order_id',$order_id)->get();
        $shipment_detail = Shipment_detail::selectRaw("shipment_details.* ")
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->where('shipments.order_id',$order_id)
            ->where('shipments.is_activated','0')
            ->get();
        return view('order.shipment')->with(compact('shipping_method','product','shipment','shipment_detail'));
    }
    // Add or Update Shipment and Shipment Detail
    public function addshipment(Request $request)
    {
         $user = \Auth::user();
         $user_details = User_info::where('user_id',$user->id)->get();
         $results = Customer_amazon_detail::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken")
            ->where('user_id',$user->id)
            ->get();
         //create order
        if(empty($request->input('order_id')))
        {
            $order_detail=array('order_no'=>Uuid::generate(1,time())->string,
                                'user_id'=>$user->id
             );
             $order = new Order($order_detail);
             $order->save();
             $order_id = $order->order_id;
         }
         else
         {
             $order_id=$request->input('order_id');
         }
        $request->session()->put('order_id', $order_id);
        // set values for shipment api call this are common for all
        $UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
        $UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
        $fromaddress= new \FBAInboundServiceMWS_Model_Address();
        $fromaddress->setName($user_details[0]->company_name);
        $fromaddress->setAddressLine1($user_details[0]->company_address);
        $fromaddress->setCountryCode($user_details[0]->company_country);
        $fromaddress->setStateOrProvinceCode($user_details[0]->company_state);
        $fromaddress->setCity($user_details[0]->company_city);
        $fromaddress->setPostalCode($user_details[0]->company_zipcode);
        //delete shipment2
        if($request->input('split_shipment')=='0') {
            if (!empty($request->input('shipment_id2'))) {
                //when shipment2 delete whole shipments update with 0 qty
                $destinations= Amazon_destination::where('shipment_id',$request->input('shipment_id2'))->get();
                $update_service = $this->getReportsClient();
                $shipment_request = new \FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest();
                $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
                $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                foreach ($destinations as $remove_destination) {
                    $shipment_header = new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                    $shipment_header->setShipmentName("SHIPMENT_NAME");
                    $shipment_header->setShipFromAddress($fromaddress);
                    $shipment_header->setDestinationFulfillmentCenterId($remove_destination->destination_name);
                    $shipment_request->setInboundShipmentHeader($shipment_header);
                    $shipment_request->setShipmentId($remove_destination->api_shipment_id);
                    $item_array=array();
                    $item_array = array('SellerSKU' => isset($remove_destination->sellerSKU) ? $remove_destination->sellerSKU : '', 'QuantityShipped' => '0');
                    $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                    $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                    $api_shipment_detail->setmember($shipment_item);
                    $shipment_request->setInboundShipmentItems($api_shipment_detail);
                    $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
                }
                Amazon_destination::where('shipment_id',$request->input('shipment_id2'))->delete();
                $sub_count = $request->input('count2');
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input("shipment_detail2_" . $sub_cnt))) {
                        $shipment_detail_id=$request->input("shipment_detail2_" . $sub_cnt);
                        Listing_service_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Prep_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Product_labels_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Supplier_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                        Shipment_detail::where('shipment_detail_id',$shipment_detail_id)->delete();
                    }
                }
                Shipments::where('shipment_id',$request->input('shipment_id2'))->delete();
            }
        }
        for ($cnt = 1; $cnt <= $request->input('ship_count'); $cnt++) {
            //update shipment and shipment detail
            if(!empty($request->input('shipment_id'.$cnt)))
            {
                $shipment = array('order_id'=>$order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
                $sub_count=$request->input('count'.$cnt);
                //set values for shipment api call when new product add or update common for them
                $update_service = $this->getReportsClient();
                $shipment_request = new \FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest();
                $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
                $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                $shipment_header= new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                $shipment_header->setShipmentName("SHIPMENT_NAME");
                $shipment_header->setShipFromAddress($fromaddress);

                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
                    //every product update
                    if (!empty($request->input("shipment_detail" . $cnt . "_" . $sub_cnt))) {

                        $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                        $shipment_details = array(
                            'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                            'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                            'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                            'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                            'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                        );
                        // when product update with another product then old update with 0 qty and new product add
                        $old_destination=Amazon_destination::where('shipment_id',$request->input('shipment_id'.$cnt))->where('fulfillment_network_SKU',$request->input('original_upc_fnsku' . $cnt . "_" . $sub_cnt))->get();
                        if($request->input('original_upc_fnsku'.$cnt."_".$sub_cnt)!=$request->input('upc_fnsku' . $cnt . "_" . $sub_cnt))
                        {

                            foreach ($old_destination as $remove_destination) {
                                $item_array=array();
                                $shipment_header->setDestinationFulfillmentCenterId($remove_destination->destination_name);
                                $shipment_request->setInboundShipmentHeader($shipment_header);
                                $shipment_request->setShipmentId($remove_destination->api_shipment_id);
                                $item_array = array('SellerSKU' => isset($remove_destination->sellerSKU) ? $remove_destination->sellerSKU : '', 'QuantityShipped' => '0');
                                $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                                $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                                $api_shipment_detail->setmember($shipment_item);
                                $shipment_request->setInboundShipmentItems($api_shipment_detail);
                                $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
                            }
                            $destination_name=isset($old_destination[0]->destination_name) ? $old_destination[0]->destination_name : '';
                            $api_shipment_id=isset($old_destination[0]->api_shipment_id) ? $old_destination[0]->api_shipment_id :'';
                            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
                            $shipment_request->setInboundShipmentHeader($shipment_header);
                            $shipment_request->setShipmentId($api_shipment_id);
                            $item_array=array();
                            $item_array= array('SellerSKU'=>$request->input('sellersku'. $cnt . "_" . $sub_cnt),'QuantityShipped'=>$request->input('total'. $cnt . "_" . $sub_cnt));
                            $shipment_item= new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                            $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                            $api_shipment_detail->setmember($shipment_item);
                            $shipment_request->setInboundShipmentItems($api_shipment_detail);
                            $update_response=$this->invokeUpdateInboundShipment($update_service, $shipment_request);
                            $amazon_destination = array('destination_name'=>$destination_name,
                                'shipment_id'=>$request->input('shipment_id' . $cnt),
                                'api_shipment_id'=>$api_shipment_id,
                                'sellerSKU'=>$request->input('sellersku'. $cnt . "_" . $sub_cnt),
                                'fulfillment_network_SKU'=>$request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                                'qty'=>$request->input('total' . $cnt . "_" . $sub_cnt),
                                'ship_to_address_name'=>isset($old_destination[0]->ship_to_address_name) ? $old_destination[0]->ship_to_address_name :'',
                                'ship_to_address_line1'=>isset($old_destination[0]->ship_to_address_line1) ? $old_destination[0]->ship_to_address_line1 :'',
                                'ship_to_city'=>isset($old_destination[0]->ship_to_city) ? $old_destination[0]->ship_to_city :'',
                                'ship_to_state_code'=>isset($old_destination[0]->ship_to_state_code) ? $old_destination[0]->ship_to_state_code :'',
                                'ship_to_country_code'=>isset($old_destination[0]->ship_to_country_code) ? $old_destination[0]->ship_to_country_code :'',
                                'ship_to_postal_code'=>isset($old_destination[0]->ship_to_postal_code) ? $old_destination[0]->ship_to_postal_code :'',
                                'label_prep_type'=>isset($old_destination[0]->label_prep_type) ? $old_destination[0]->label_prep_type :'',
                                'total_units'=>$request->input('total' . $cnt . "_" . $sub_cnt),
                                'fee_per_unit_currency_code'=>isset($old_destination[0]->fee_per_unit_currency_code) ? $old_destination[0]->fee_per_unit_currency_code :'',
                                'fee_per_unit_value'=>isset($old_destination[0]->fee_per_unit_value) ? $old_destination[0]->fee_per_unit_value :'',
                                'total_fee_value'=>isset($old_destination[0]->fee_per_unit_value) ? $request->input('total' . $cnt . "_" . $sub_cnt)*$old_destination[0]->fee_per_unit_value:$request->input('total' . $cnt . "_" . $sub_cnt)
                            );
                            Amazon_destination::create($amazon_destination);
                            Amazon_destination::where('shipment_id',$request->input('shipment_id'.$cnt))->where('fulfillment_network_SKU',$request->input('original_upc_fnsku' . $cnt . "_" . $sub_cnt))->delete();
                        }
                        //when product's qty change then update shipments with new qty
                        else if($request->input('original_total'.$cnt."_".$sub_cnt)!=$request->input('total' . $cnt . "_" . $sub_cnt))
                        {
                            $diff_qty=0;
                            if($request->input('total' . $cnt . "_" . $sub_cnt) > $request->input('original_total'.$cnt."_".$sub_cnt))
                            {
                                $diff_qty=  $request->input('total' . $cnt . "_" . $sub_cnt)- $request->input('original_total'.$cnt."_".$sub_cnt);
                                $new_qty= $diff_qty+$old_destination[0]->qty;
                                $shipment_header->setDestinationFulfillmentCenterId($old_destination[0]->destination_name);
                                $shipment_request->setInboundShipmentHeader($shipment_header);
                                $shipment_request->setShipmentId($old_destination[0]->api_shipment_id);
                                $item_array=array();
                                $item_array = array('SellerSKU' => isset($old_destination[0]->sellerSKU) ? $old_destination[0]->sellerSKU : '', 'QuantityShipped' => $new_qty);
                                $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                                $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                                $api_shipment_detail->setmember($shipment_item);
                                $shipment_request->setInboundShipmentItems($api_shipment_detail);
                                $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
                                $update_quantity= array('qty'=>$new_qty);
                                Amazon_destination::where('amazon_destination_id',$old_destination[0]->amazon_destination_id)->update($update_quantity);
                            }
                            else if($request->input('total' . $cnt . "_" . $sub_cnt) < $request->input('original_total'.$cnt."_".$sub_cnt))
                            {
                               $diff_qty=  $request->input('original_total' . $cnt . "_" . $sub_cnt)- $request->input('total'.$cnt."_".$sub_cnt);
                               foreach ($old_destination as $update_destination)
                               {

                                $diff_qty=$update_destination->qty-abs($diff_qty);
                                if($diff_qty<0)
                                {

                                    $shipment_header->setDestinationFulfillmentCenterId($update_destination->destination_name);
                                    $shipment_request->setInboundShipmentHeader($shipment_header);
                                    $shipment_request->setShipmentId($update_destination->api_shipment_id);
                                    $item_array=array();
                                    $item_array = array('SellerSKU' => isset($update_destination->sellerSKU) ? $update_destination->sellerSKU : '', 'QuantityShipped' => '0');
                                    $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                                    $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                                    $api_shipment_detail->setmember($shipment_item);
                                    $shipment_request->setInboundShipmentItems($api_shipment_detail);
                                    $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
                                    $update_quantity= array('qty'=>'0');
                                    Amazon_destination::where('amazon_destination_id',$update_destination->amazon_destination_id)->update($update_quantity);
                                }
                                if($diff_qty>=0) {
                                    $shipment_header->setDestinationFulfillmentCenterId($update_destination->destination_name);
                                    $shipment_request->setInboundShipmentHeader($shipment_header);
                                    $shipment_request->setShipmentId($update_destination->api_shipment_id);
                                    $item_array=array();
                                    $item_array = array('SellerSKU' => isset($update_destination->sellerSKU) ? $update_destination->sellerSKU : '', 'QuantityShipped' => $diff_qty);
                                    $shipment_item = new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                                    $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                                    $api_shipment_detail->setmember($shipment_item);
                                    $shipment_request->setInboundShipmentItems($api_shipment_detail);
                                    $update_response = $this->invokeUpdateInboundShipment($update_service, $shipment_request);
                                    $update_quantity = array('qty' => $diff_qty);
                                    Amazon_destination::where('amazon_destination_id', $update_destination->amazon_destination_id)->update($update_quantity);
                                    break;
                                }
                               }

                            }
                        }
                        Shipment_detail::where('shipment_detail_id', $request->input("shipment_detail" . $cnt . "_" . $sub_cnt))->update($shipment_details);
                    }
                    //new product add in current shipment
                    else
                    {
                        $item_array=array();
                        $destinations= Amazon_destination::where('shipment_id',$request->input('shipment_id' . $cnt))->groupby('shipment_id')->get();
                        if(!empty($request->input('product_desc'.$cnt."_".$sub_cnt))) {
                            $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                            $shipment_details = array('shipment_id' => $request->input('shipment_id' . $cnt),
                                'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                                'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                                'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                                'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                                'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                            );
                            $shipment_detail = new Shipment_detail($shipment_details);
                            $shipment_detail->save();
                            $destination_name=isset($destinations[0]->destination_name) ? $destinations[0]->destination_name : '';
                            $api_shipment_id=isset($destinations[0]->api_shipment_id) ? $destinations[0]->api_shipment_id :'';
                            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
                            $shipment_request->setInboundShipmentHeader($shipment_header);
                            $shipment_request->setShipmentId($api_shipment_id);
                            $item_array=array();
                            $item_array= array('SellerSKU'=>$request->input('sellersku'. $cnt . "_" . $sub_cnt),'QuantityShipped'=>$request->input('total' . $cnt . "_" . $sub_cnt));
                            $shipment_item= new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
                            $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
                            $api_shipment_detail->setmember($shipment_item);
                            $shipment_request->setInboundShipmentItems($api_shipment_detail);
                            $update_response=$this->invokeUpdateInboundShipment($update_service, $shipment_request);
                            $amazon_destination = array('destination_name'=>$destination_name,
                                'shipment_id'=>$request->input('shipment_id' . $cnt),
                                'api_shipment_id'=>$api_shipment_id,
                                'sellerSKU'=>$request->input('sellersku'. $cnt . "_" . $sub_cnt),
                                'fulfillment_network_SKU'=>$request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                                'qty'=>$request->input('total' . $cnt . "_" . $sub_cnt),
                                'ship_to_address_name'=>isset($destinations[0]->ship_to_address_name) ? $destinations[0]->ship_to_address_name :'',
                                'ship_to_address_line1'=>isset($destinations[0]->ship_to_address_line1) ? $destinations[0]->ship_to_address_line1 :'',
                                'ship_to_city'=>isset($destinations[0]->ship_to_city) ? $destinations[0]->ship_to_city :'',
                                'ship_to_state_code'=>isset($destinations[0]->ship_to_state_code) ? $destinations[0]->ship_to_state_code :'',
                                'ship_to_country_code'=>isset($destinations[0]->ship_to_country_code) ? $destinations[0]->ship_to_country_code :'',
                                'ship_to_postal_code'=>isset($destinations[0]->ship_to_postal_code) ? $destinations[0]->ship_to_postal_code :'',
                                'label_prep_type'=>isset($destinations[0]->label_prep_type) ? $destinations[0]->label_prep_type :'',
                                'total_units'=>$request->input('total' . $cnt . "_" . $sub_cnt),
                                'fee_per_unit_currency_code'=>isset($destinations[0]->fee_per_unit_currency_code) ? $destinations[0]->fee_per_unit_currency_code :'',
                                'fee_per_unit_value'=>isset($destinations[0]->fee_per_unit_value) ? $destinations[0]->fee_per_unit_value :'',
                                'total_fee_value'=>isset($destinations[0]->fee_per_unit_value) ? $request->input('total' . $cnt . "_" . $sub_cnt)*$destinations[0]->fee_per_unit_value:$request->input('total' . $cnt . "_" . $sub_cnt)
                            );
                            Amazon_destination::create($amazon_destination);
                        }

                    }
                }
            }
            //insert shipment and shipment detail
            else {
                $shipment = array('order_id'=>$order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                $shipment = new Shipments($shipment);
                $shipment->save();
                $last_id = $shipment->shipment_id;
                //create shipmentplan api
                $service = $this->getReportsClient();
                $ship_request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest();
                $ship_request->setSellerId($UserCredentials['mws_seller_id']);
                $ship_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                $ship_request->setShipFromAddress($fromaddress);
                $item=array();
                $sub_count=$request->input('count'.$cnt);
                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
                    if(!empty($request->input('product_desc'.$cnt."_".$sub_cnt))) {
                        $product_id = explode(' ', $request->input('product_desc' . $cnt . "_" . $sub_cnt));
                        $shipment_details = array('shipment_id' => $last_id,
                            'product_id' => isset($product_id[1]) ? $product_id[1] : '',
                            'fnsku' => $request->input('upc_fnsku' . $cnt . "_" . $sub_cnt),
                            'qty_per_box' => $request->input('qty_per_case' . $cnt . "_" . $sub_cnt),
                            'no_boxs' => $request->input('no_of_case' . $cnt . "_" . $sub_cnt),
                            'total' => $request->input('total' . $cnt . "_" . $sub_cnt)
                        );
                        $shipment_detail = new Shipment_detail($shipment_details);
                        $shipment_detail->save();
                        $data =array('SellerSKU'=>$request->input('sellersku'. $cnt . "_" . $sub_cnt),'Quantity'=>$request->input('total' . $cnt . "_" . $sub_cnt));
                        $item[] = new \FBAInboundServiceMWS_Model_InboundShipmentPlanItem($data);
                    }
                }
                $itemlist = new \FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList();
                $itemlist->setmember($item);
                $ship_request->setInboundShipmentPlanRequestItems($itemlist);
                $arr_response =$this->invokeCreateInboundShipmentPlan($service, $ship_request);
                $shipment_id=$last_id;
                //create shipments api of perticular shipmentplan
                $shipment_service = $this->getReportsClient();
                $shipment_request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentRequest();
                $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
                $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
                $shipment_header= new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
                $shipment_header->setShipmentName("SHIPMENT_NAME");
                $shipment_header->setShipFromAddress($fromaddress);
                //response of shipment plan and insert data in amazon destination
                foreach ($arr_response as $new_response) {
                    foreach ($new_response->InboundShipmentPlans as $planresult) {
                        foreach ($planresult->member as $member) {
                            $api_shipment_id = $member->ShipmentId;
                            $destination_name = $member->DestinationFulfillmentCenterId;
                            foreach ($member->ShipToAddress as $address)
                            {
                                $address_name=$address->Name;
                                $addressline1=$address->AddressLine1;
                                $city=$address->City;
                                $state=$address->StateOrProvinceCode;
                                $country=$address->CountryCode;
                                $postal=$address->PostalCode;
                            }
                            $preptype=$member->LabelPrepType;
                            foreach ($member->EstimatedBoxContentsFee as $fee)
                            {
                                $total_unit=$fee->TotalUnits;
                                foreach ($fee->FeePerUnit as $unit)
                                {
                                    $unit_currency=$unit->CurrencyCode;
                                    $unit_value=$unit->Value;
                                }
                                foreach ($fee->TotalFee as $total)
                                {
                                    $total_currency=$total->CurrencyCode;
                                    $total_value=$total->Value;
                                }
                            }
                            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
                            $shipment_request->setInboundShipmentHeader($shipment_header);
                            $shipment_request->setShipmentId($api_shipment_id);
                            $shipment_item=array();
                            foreach ($member->Items as $item)
                            {
                                foreach ($item->member as $sub_member)
                                {
                                    $amazon_destination = array('destination_name'=>$destination_name,
                                        'shipment_id'=>$shipment_id,
                                        'api_shipment_id'=>$api_shipment_id,
                                        'sellerSKU'=>$sub_member->SellerSKU,
                                        'fulfillment_network_SKU'=>$sub_member->FulfillmentNetworkSKU,
                                        'qty'=>$sub_member->Quantity,
                                        'ship_to_address_name'=>$address_name,
                                        'ship_to_address_line1'=>$addressline1,
                                        'ship_to_city'=>$city,
                                        'ship_to_state_code'=>$state,
                                        'ship_to_country_code'=>$country,
                                        'ship_to_postal_code'=>$postal,
                                        'label_prep_type'=>$preptype,
                                        'total_units'=>$total_unit,
                                        'fee_per_unit_currency_code'=>$unit_currency,
                                        'fee_per_unit_value'=>$unit_value,
                                        'total_fee_value'=>$total_value
                                    );
                                    Amazon_destination::create($amazon_destination);

                                    $item_array= array('SellerSKU'=>$sub_member->SellerSKU, 'QuantityShipped'=>$sub_member->Quantity, 'FulfillmentNetworkSKU'=>$sub_member->FulfillmentNetworkSKU);
                                    $shipment_item[]= new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
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
        }
        $order_detail=array('steps'=>'1');
        Order::where('order_id',$order_id)->update($order_detail);
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
    }

    protected function getReportsClient()
    {
        list($access_key, $secret_key, $config) = $this->getKeys();
        return  new \FBAInboundServiceMWS_Client(
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
        $devAccount = Dev_account::first();
        return [
            $devAccount->access_key,
            $devAccount->secret_key,
            self::getMWSConfig()
        ];
    }
    public static function getMWSConfig()
    {
        return [
            'ServiceURL' =>"https://mws.amazonservices.com/FulfillmentInboundShipment/2010-10-01" ,
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
    public function removeproduct(Request $request)
    {
        if ($request->ajax()) {
            $post = $request->all();
             $fnsku=$post['fnsku'];
             $shipment_id=$post['shipment_id'];
            $user = \Auth::user();
            $user_details = User_info::where('user_id',$user->id)->get();
            $results = Customer_amazon_detail::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken")
                ->where('user_id',$user->id)
                ->get();
            $destinations= Amazon_destination::where('shipment_id',$shipment_id)->where('fulfillment_network_SKU',$fnsku)->groupby('shipment_id')->get();
            $UserCredentials['mws_authtoken'] = !empty($results[0]->mws_authtoken) ? decrypt($results[0]->mws_authtoken) : '';
            $UserCredentials['mws_seller_id'] = !empty($results[0]->mws_seller_id) ? decrypt($results[0]->mws_seller_id) : '';
            $destination_name=isset($destinations[0]->destination_name) ? $destinations[0]->destination_name : '';
            $api_shipment_id=isset($destinations[0]->api_shipment_id) ? $destinations[0]->api_shipment_id :'';
            $update_service = $this->getReportsClient();
            $shipment_request = new \FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest();
            $shipment_request->setSellerId($UserCredentials['mws_seller_id']);
            $shipment_request->setMWSAuthToken($UserCredentials['mws_authtoken']);
            $fromaddress= new \FBAInboundServiceMWS_Model_Address();
            $fromaddress->setName($user_details[0]->company_name);
            $fromaddress->setAddressLine1($user_details[0]->company_address);
            $fromaddress->setCountryCode($user_details[0]->company_country);
            $fromaddress->setStateOrProvinceCode($user_details[0]->company_state);
            $fromaddress->setCity($user_details[0]->company_city);
            $fromaddress->setPostalCode($user_details[0]->company_zipcode);
            $shipment_header= new \FBAInboundServiceMWS_Model_InboundShipmentHeader();
            $shipment_header->setShipmentName("SHIPMENT_NAME");
            $shipment_header->setShipFromAddress($fromaddress);
            $shipment_header->setDestinationFulfillmentCenterId($destination_name);
            $shipment_request->setInboundShipmentHeader($shipment_header);
            $shipment_request->setShipmentId($api_shipment_id);
            $item_array=array();
            $item_array= array('SellerSKU'=>isset($destinations[0]->sellerSKU) ? $destinations[0]->sellerSKU : '','QuantityShipped'=>'0');
            $shipment_item[]= new \FBAInboundServiceMWS_Model_InboundShipmentItem($item_array);
            $api_shipment_detail = new \FBAInboundServiceMWS_Model_InboundShipmentItemList();
            $api_shipment_detail->setmember($shipment_item);
            $shipment_request->setInboundShipmentItems($api_shipment_detail);
            $update_response=$this->invokeUpdateInboundShipment($update_service, $shipment_request);
            Amazon_destination::where('fulfillment_network_SKU',$fnsku)->where('shipment_id',$shipment_id)->delete();
            Listing_service_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Prep_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Product_labels_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Supplier_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
            Shipment_detail::where('shipment_detail_id',$post['shipment_detail_id'])->delete();
        }
    }
    public function supplierdetail(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $product = Shipment_detail::selectRaw("shipments.order_id, shipment_details.shipment_detail_id,supplier_details.supplier_id,  supplier_details.supplier_detail_id,  shipment_details.product_id, shipment_details.total,  amazon_inventories.product_name  ")
            ->join('supplier_details','shipment_details.shipment_detail_id','=','supplier_details.shipment_detail_id','left')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id','left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->where('shipments.order_id',$order_id)
            ->get();
        $supplier = Supplier::where('user_id',$user->id)->get();
        return view('order.supplier')->with(compact('product', 'supplier'));
    }
    public function addsupplierdetail(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('supplier_detail_id'.$cnt))) {
                $supplier = array('shipment_detail_id'=>$request->input('shipment_detail_id' . $cnt),
                    'order_id' => $request->input('order_id'),
                    'supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                $supplier_detail = new Supplier_detail($supplier);
                $supplier_detail->save();
            }
            else{
                $supplier = array('supplier_id' => $request->input('supplier' . $cnt),
                    'user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_unit' => $request->input('total' . $cnt)
                );
                Supplier_detail::where('supplier_detail_id',$request->input('supplier_detail_id'.$cnt))->update($supplier);
                $supplier_inspection= array('supplier_id'=>$request->input('supplier' . $cnt),'is_inspection'=>'0','inspection_decription'=>'');
                Supplier_inspection::where('supplier_detail_id',$request->input('supplier_detail_id'.$cnt))->update($supplier_inspection);
            }
        }
        $order_detail=array('steps'=>'2');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }
    public function addsupplier(Request $request)
    {
        if ($request->ajax()) {
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

    }
    public function preinspection(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $supplier = Supplier::selectRaw("supplier_inspections.is_inspection, supplier_inspections.inspection_decription, suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id','left')
            ->join('supplier_inspections','supplier_details.supplier_detail_id','=','supplier_inspections.supplier_detail_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->distinct('supplier_inspections.supplier_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.order_id, supplier_inspections.supplier_inspection_id, supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->join('supplier_inspections','supplier_inspections.supplier_detail_id','=','supplier_details.supplier_detail_id','left')
            ->where('supplier_details.order_id', $order_id)
            ->distinct('supplier_inspections.is_inspection')
            ->get();
        return view('order.pre_inspection')->with(compact('product', 'supplier'));
    }
    public function addpreinspection(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_count=$request->input('product_count'.$cnt);
            for($product_cnt=1; $product_cnt< $product_count; $product_cnt++) {
                if(empty($request->input('supplier_inspection_id'.$cnt."_".$product_cnt))) {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'order_id'=>$request->input('order_id'),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc'.$cnt),
                        'supplier_id' => $request->input('supplier_id'.$cnt)
                    );
                    $supplier_inspection = new Supplier_inspection($supplier);
                    $supplier_inspection->save();
                }
                else
                {
                    $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id' . $cnt . "_" . $product_cnt),
                        'user_id' => $user->id,
                        'is_inspection' => $request->input('inspection' . $cnt),
                        'inspection_decription' => $request->input('inspection_desc' . $cnt),
                        'supplier_id' => $request->input('supplier_id'.$cnt)
                    );
                    Supplier_inspection::where('supplier_inspection_id',$request->input('supplier_inspection_id'.$cnt."_".$product_cnt))->update($supplier);
                }
            }
        }
        $order_detail=array('steps'=>'3');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/productlabels')->with('Success', 'Pre inspection Information Added Successfully');
    }
    public function labels(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $product_label= Product_labels::all();
        $product = Shipment_detail::selectRaw(" shipments.order_id, product_labels_details.product_label_detail_id, product_labels_details.product_label_id, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('product_labels_details','shipment_details.shipment_detail_id','=','product_labels_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->groupby('shipment_details.shipment_detail_id')
            ->get();
        return view('order.product_labels')->with(compact('product', 'product_label'));
    }
    public function addlabels(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('product_label_detail_id'.$cnt))) {

                $product_label = array('order_id'=>$request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => $request->input('labels' . $cnt),
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt)
                );
                $product_labels_detail = new Product_labels_detail($product_label);
                $product_labels_detail->save();
            }
            else
            {
                $product_label = array(
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'product_label_id' => $request->input('labels' . $cnt),
                    'fbsku' => $request->input('sku' . $cnt),
                    'qty' => $request->input('total' . $cnt)
                );
                Product_labels_detail::where('product_label_detail_id',$request->input('shipment_detail_id'.$cnt))->update($product_label);
            }
        }
        $order_detail=array('steps'=>'4');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/prepservice')->with('Success', 'Product Label Information Added Successfully');
    }
    public function prepservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $prep_service= Prep_service::all();
        $product = Shipment_detail::selectRaw("shipments.order_id, prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.grand_total, prep_details.prep_service_ids, shipment_details.shipment_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id','left')
            ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
        return view('order.prep_service')->with(compact('prep_service', 'product'));
    }
    public function addprepservice(Request $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service=array();
            $sub_count =$request->input('sub_count'.$cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[]=$request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if(empty($request->input('prep_detail_id'.$cnt))) {
                $prep_service = array('user_id' => $user->id,
                    'order_id'=>$request->input('order_id'),
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => implode(',', $service),
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                $prep_service_detail = new Prep_detail($prep_service);
                $prep_service_detail->save();
            }
            else
            {
                $prep_service = array('user_id' => $user->id,
                    'shipment_detail_id' => $request->input('shipment_detail_id' . $cnt),
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => implode(',', $service),
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Prep_detail::where('prep_detail_id',$request->input('prep_detail_id'.$cnt))->update($prep_service);
            }
        }
        $order_detail=array('steps'=>'5');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/listservice')->with('Success', 'Prep Service Information Added Successfully');
    }
    public function listservice(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $list_service= Listing_service::all();
        $product = Shipment_detail::selectRaw("shipments.order_id, listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.grand_total, listing_service_details.listing_service_ids,shipment_details.product_id, shipment_details.shipment_detail_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->join('listing_service_details','listing_service_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
            ->where('shipments.order_id', $order_id)
            ->get();
      return view('order.list_service')->with(compact('list_service', 'product'));
    }
    public function addlistservice(Request $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service=array();
            $sub_count =$request->input('sub_count'.$cnt);
            for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                if (!empty($request->input("service" . $cnt . "_" . $sub_cnt))) {
                    $service[]=$request->input('service' . $cnt . "_" . $sub_cnt);
                }
            }
            if(empty($request->input('listing_service_detail_id'.$cnt))) {
                $list_service = array('order_id'=>$request->input('order_id'),
                        'product_id' => $request->input('product_id' . $cnt),
                        'listing_service_ids' => implode(',', $service),
                        'shipment_detail_id' => $request->input('shipment_detail_id'.$cnt),
                        'listing_service_total' => $request->input('total' . $cnt),
                        'grand_total' => $request->input('grand_total')
                    );
                    $list_service_detail = new Listing_service_detail($list_service);
                    $list_service_detail->save();

            }
            else
            {
                $list_service = array(
                    'product_id' => $request->input('product_id' . $cnt),
                    'listing_service_ids' => implode(',', $service),
                    'shipment_detail_id' => $request->input('shipment_detail_id'.$cnt),
                    'listing_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                Listing_service_detail::where('listing_service_detail_id',$request->input('listing_service_detail_id'.$cnt))->update($list_service);
            }
        }
        $order_detail=array('steps'=>'6');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/outbondshipping')->with('Success', 'Listing service Information Added Successfully');
    }
    public function outbondshipping(Request $request)
    {
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $outbound_method= Outbound_method::all();
        $shipment =Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, shipments.order_id")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id')
            ->where('shipments.order_id',$order_id)
            ->orderby('shipments.shipment_id')
            ->get();
        $product = Amazon_destination::selectRaw("shipments.order_id, shipments.shipment_id, amazon_inventories.id, amazon_inventories.product_name, amazon_destinations.destination_name, amazon_destinations.qty, amazon_destinations.amazon_destination_id")
            ->join('amazon_inventories', 'amazon_inventories.sellerSKU', '=', 'amazon_destinations.sellerSKU','left')
            ->join('shipments','amazon_destinations.shipment_id','=','shipments.shipment_id','left')
            ->where('shipments.order_id', $order_id)
            ->where('amazon_inventories.user_id', $user->id)
            ->get();
        foreach ($shipment as $shipments)
        {
            $data=array();
            $data['order']=$shipments->order_id;
            $data['shipment_id']=$shipments->shipment_id;
            $data['shipment_name']=$shipments->shipping_name;

            foreach ($product as $products)
            {

                if($shipments->shipment_id==$products->shipment_id)
                {

                    $new_data= array('product_name'=>$products->product_name,
                                     'product_id'=>$products->id,
                                    'qty'=>$products->qty,
                                    'destination_id'=>$products->amazon_destination_id
                        );
                        $outbound_shipping_details= Outbound_Shipping_detail::where('amazon_destination_id',$products->amazon_destination_id)->get();
                       $data['outbound_shipping_detail_ids'][$products->amazon_destination_id] =(count($outbound_shipping_details)>0) ? $outbound_shipping_details[0]->outbound_shipping_detail_id:null;
                       $data['outbound_method_ids'][$products->amazon_destination_id] =(count($outbound_shipping_details)>0) ?$outbound_shipping_details[0]->outbound_method_id:null;
                       $data['destination'][$products->destination_name][]=$new_data;

                }

            }

            $detail[]=$data;
        }
        return view('order.outbound_shipping')->with(compact('outbound_method', 'detail','outbound_detail'));
    }
    public function addoutbondshipping(Request $request)
    {
      $ship_count = $request->input('ship_count');
        for ($ship_cnt = 1; $ship_cnt < $ship_count; $ship_cnt++) {
            $count = $request->input('count' . $ship_cnt);
            for ($cnt = 1; $cnt < $count; $cnt++) {
                $product_count = $request->input("product_count" . $ship_cnt . "_" . $cnt);
                for ($product_cnt = 1; $product_cnt < $product_count; $product_cnt++) {
                    if (empty($request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt."_".$product_cnt))) {
                        $outbound_shipping = array("amazon_destination_id" => $request->input('amazon_destination_id' . $ship_cnt . "_" . $cnt . "_" . $product_cnt),
                            "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                            "shipment_id" => $request->input('shipment_id' . $ship_cnt),
                            "order_id" => $request->input('order_id'),
                            "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt . "_" . $product_cnt),
                            "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt . "_" . $product_cnt)
                        );
                        $outbound_shipping_detail = new Outbound_Shipping_detail($outbound_shipping);
                        $outbound_shipping_detail->save();
                    } else {
                        $outbound_shipping = array("amazon_destination_id" => $request->input('amazon_destination_id' . $ship_cnt . "_" . $cnt . "_" . $product_cnt),
                            "outbound_method_id" => $request->input('outbound_method' . $ship_cnt . "_" . $cnt),
                            "shipment_id" => $request->input('shipment_id' . $ship_cnt),
                            "order_id" => $request->input('order_id'),
                            "product_ids" => $request->input('product_id' . $ship_cnt . "_" . $cnt . "_" . $product_cnt),
                            "qty" => $request->input('total_unit' . $ship_cnt . "_" . $cnt . "_" . $product_cnt)
                        );
                        Outbound_Shipping_detail::where('outbound_shipping_detail_id', $request->input("outbound_shipping_detail_id" . $ship_cnt . "_" . $cnt."_".$product_cnt))->update($outbound_shipping);
                    }
                }
            }
        }
        $order_detail=array('steps'=>'7');
        Order::where('order_id',$request->input('order_id'))->update($order_detail);
        return redirect('order/reviewshipment')->with('Success', 'Outbound Shipping Information Added Successfully');
    }
    public function reviewshipment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $shipment = Shipments::selectRaw("shipments.shipment_id, shipping_methods.shipping_name, sum(shipment_details.total) as total")
            ->join('shipping_methods','shipments.shipping_method_id','=','shipping_methods.shipping_method_id')
            ->join('shipment_details','shipments.shipment_id','=','shipment_details.shipment_id')
            ->where('shipments.order_id',$order_id)
            ->groupby('shipment_details.shipment_id')
            ->get();
        $outbound_detail= Outbound_Shipping_detail::selectRaw('amazon_destinations.destination_name, sum(outbound_shipping_details.qty) as total, outbound_methods.outbound_name')
            ->join('amazon_destinations','outbound_shipping_details.amazon_destination_id','=','amazon_destinations.amazon_destination_id','left')
            ->join('outbound_methods','outbound_shipping_details.outbound_method_id','=','outbound_methods.outbound_method_id','left')
            ->where('outbound_shipping_details.order_id',$order_id)
            ->groupby('amazon_destinations.destination_name','outbound_shipping_details.outbound_method_id')
            ->get();
        $product_detail= Shipment_detail::selectRaw('shipments.order_id, amazon_inventories.product_name, shipment_details.total, prep_details.prep_service_ids')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id','left')
            ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id')
            ->where('shipments.order_id',$order_id)
            ->get();
        $prep_service= Prep_service::all();
        return view('order.review_shipment')->with(compact('shipment','outbound_detail','product_detail','prep_service'));
    }
    public function orderpayment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $order_detail=array('steps'=>'8');
        Order::where('order_id',$order_id)->update($order_detail);
        $supplier = Supplier_detail::where('order_id',$order_id)->groupby('supplier_id')->get();
        $supplier_count=count($supplier);
        $pre_shipment_inspection=400*$supplier_count;
        $label=Product_labels_detail::SelectRaw('sum(qty) as total')->where('order_id',$order_id)->groupby('product_label_id')->get();
        $label_total=0;
        foreach ($label as $labels)
        {
            $label_total+=$labels->total*0.15;
        }
        $price=array('pre_shipment_inspection'=>$pre_shipment_inspection,
            'shipping_cost'=>'0',
            'port_fee'=>'0',
            'custom_brokerage'=>'0',
            'custom_duty'=>'0',
            'consult_charge'=>'0',
            'label_charge'=>$label_total,
            'prep_forwarding'=>'4',
            'listing_service'=>'0',
            'inbound_shipping'=>'0',
        );
        $card_type= array('visa'=>'visa',
            'mastercard'=>'mastercard',
            'amex'=>'amex',
            'discover'=>'discover',
            'maestro'=>'maestro'
        );
        $user = \Auth::user();
        $addresses =Addresses::where('user_id', $user->id)->where('type','B')->get();
        $credit_card= User_credit_cardinfo::where('user_id',$user->id)->get();
        return view('order.payment')->with(compact('price','card_type','addresses','credit_card'));
    }
    public function addcreditcard(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    env('CLIENT_ID'),
                    env('SECRET_KEY')
                )
            );
            $date = explode('-',$request->input('expire_card'));
            $card = new CreditCard();
            $card->setType($request->input('credit_card_type'))
                ->setNumber($request->input('credit_card_number'))
                ->setExpireMonth($date[0])
                ->setExpireYear($date[1])
                ->setCvv2($request->input('cvv'))
                ->setFirstName($request->input('first_name'))
                ->setLastName($request->input('last_name'));

            try {
                $card->create($apiContext);
            }
            catch (\PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            }
            catch (Exception $ex) {
                die($ex);
            }
            $card_detail=array('user_id'=>$user->id,
                'credit_card_type'=>$card->type,
                'credit_card_number'=>$card->number,
                'credit_card_id' =>$card->id
            );
            User_credit_cardinfo::create($card_detail);
        }
    }
    public function addaddress(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $address_detail=array('user_id'=>$user->id,
                'type'=>'B',
                'address_1'=>$request->input('address_line_1'),
                'address_2'=>$request->input('address_line_2'),
                'city' =>$request->input('city'),
                'state' =>$request->input('state'),
                'postal_code' =>$request->input('postal_code'),
                'country' => $request->input('country')
            );
            Addresses::create($address_detail);
        }
    }
    public function addorderpayment(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $credit_card_detail=explode(' ',$request->input('credit_card_detail'));
        $payment_detail =array('address_id'=>$request->input('address'),
            'order_id' =>$order_id,
            'user_credit_cardinfo_id'=>isset($credit_card_detail[0])?$credit_card_detail[0]:'',
            'pre_shipment_inspection'=>$request->input('pre_ship_inspect'),
            'shipping_cost'=>$request->input('shipping_cost'),
            'port_fees'=>$request->input('port_fees'),
            'customs_brokerage'=>$request->input('custom_brokerage'),
            'customs_duty'=>$request->input('custom_duty'),
            'consulting_charge'=>$request->input('consulting'),
            'labels_charge'=>$request->input('label_charge'),
            'prep_forward_charge'=>$request->input('prep_forward'),
            'listing_service_charge'=>$request->input('listing_service'),
            'total_fbaforward_charge'=>$request->input('total_fbaforward'),
            'inbound_shipping_charge'=>$request->input('inbound_shipping'),
            'total_cost'=>$request->input('total_cost')
        );
        $payment_detail_id=Payment_detail::create($payment_detail);
        $last_id=$payment_detail_id->payment_detail_id;
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('CLIENT_ID'),
                env('SECRET_KEY')
            )
        );
        $creditCardToken = new CreditCardToken();
        $creditCardToken->setCreditCardId($credit_card_detail[1]);
        $fi = new FundingInstrument();
        $fi->setCreditCardToken($creditCardToken);
        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($request->input('total_cost'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
             ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));
        $request = clone $payment;
        try {
            $payment->create($apiContext);
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData();
            die($ex);
        }
        catch (Exception $ex) {
            ResultPrinter::printError("Create Payment using Saved Card", "Payment", null, $request, $ex);
            exit(1);
        }
        $payment_info=array('payment_detail_id'=>$last_id,
                            'transaction'=>$payment
            );
        Payment_info::create($payment_info);
        $order_detail=array('is_activated'=>'1','steps'=>'9');
        Order::where('order_id',$order_id)->update($order_detail);
        return redirect('order/index')->with('success','Your order Successfully Placed');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orderDetails(Request $request){
        if($request->order_id) {
            $user = \Auth::user();
            DB::enableQueryLog();
            $shipment_detail = Shipments::selectRaw("shipments.shipment_id,shipments.shipping_method_id,shipping_methods.shipping_name,shipment_details.product_id, shipment_details.fnsku, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total,amazon_inventories.product_name,supplier_details.supplier_detail_id,supplier_details.supplier_id,suppliers.company_name,supplier_inspections.inspection_decription,product_labels_details.product_label_id,product_labels.label_name,prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.prep_service_ids,listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.listing_service_ids,outbound_shipping_details.amazon_destination_id, outbound_shipping_details.outbound_method_id,outbound_methods.outbound_name,amazon_destinations.destination_name")
                ->join('shipping_methods','shipping_methods.shipping_method_id','=','shipments.shipping_method_id','left')
                ->join('shipment_details','shipment_details.shipment_id','=','shipments.shipment_id','left')
                ->join('amazon_inventories','amazon_inventories.id','=','shipment_details.product_id','left')
                ->join('supplier_details','shipment_details.shipment_detail_id','=','supplier_details.shipment_detail_id','left')
                ->join('suppliers','suppliers.supplier_id','=','supplier_details.supplier_id','left')
                ->join('supplier_inspections','supplier_inspections.supplier_detail_id','=','supplier_details.supplier_detail_id','left')
                ->join('product_labels_details','product_labels_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('product_labels','product_labels.product_label_id','=','product_labels_details.product_label_id','left')
                ->join('prep_details','prep_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('listing_service_details','listing_service_details.shipment_detail_id','=','shipment_details.shipment_detail_id','left')
                ->join('outbound_shipping_details','outbound_shipping_details.shipment_id','=','shipments.shipment_id','left')
                ->join('outbound_methods','outbound_methods.outbound_method_id','=','outbound_shipping_details.outbound_method_id','left')
                ->join('amazon_destinations','amazon_destinations.amazon_destination_id','=','outbound_shipping_details.amazon_destination_id','left')

                ->where('shipments.order_id',$request->order_id)
                ->where('shipments.user_id',$user->id)
                ->orderBy('shipments.shipment_id', 'ASC')
                ->get()->toArray();

            //$shipment_detail = $shipment_detail->toArray();


            foreach($shipment_detail as $key=>$shipment_details){
                //Fetch Prep services name
                $prep_service_ids = explode(",",$shipment_details['prep_service_ids']);
                $prep_services = Prep_service::selectRaw("service_name")->whereIn('prep_service_id', $prep_service_ids)->get();
                $service_name = array();
                if(count($prep_services)>0) {
                    foreach ($prep_services as $prep_service) {
                        $service_name[] = $prep_service->service_name;
                    }
                }
                $shipment_detail[$key]['prep_service_name'] = implode($service_name, ",");
                //Fetch Listing services name
                $listing_service_ids = explode(",",$shipment_details['listing_service_ids']);
                $listing_services = Listing_service::selectRaw("service_name")->whereIn('listing_service_id', $listing_service_ids)->get();
                $listing_service_name = array();
                if(count($listing_services)>0) {
                    foreach ($listing_services as $listing_service) {
                        $listing_service_name[] = $listing_service->service_name;
                    }
                }
                $shipment_detail[$key]['listing_service_name'] = implode($listing_service_name, ",");
            }

            // Payment Info get
            $payment_detail = Payment_detail::selectRaw('payment_details.*,user_credit_cardinfos.credit_card_number,user_credit_cardinfos.credit_card_type,user_credit_cardinfos.credit_card_id,payment_infos.transaction')
                ->join('payment_infos','payment_infos.payment_detail_id','=','payment_details.payment_detail_id','left')
                ->join('user_credit_cardinfos','user_credit_cardinfos.id','=','payment_details.user_credit_cardinfo_id','left')
                ->where('order_id',$request->order_id)->first();
            $payment_detail = $payment_detail->toArray();
            return view('order.detail_list')->with(compact('shipment_detail','payment_detail'));
        }
    }


}