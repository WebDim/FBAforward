<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

class ShipmentController extends Controller
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
        $request->session()->forget('order_id');
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment = array();
        return view('shipment.shipment')->with(compact('shipping_method', 'product', 'shipment', 'orders'));
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
        //create order
        if (empty($request->input('order_id'))) {
            $order_detail = array('order_no' => Uuid::generate(1, time())->string,
                'user_id' => $user->id
            );
            $order = new Order($order_detail);
            $order->save();
            $order_id = $order->order_id;
        } else {
            $order_id = $request->input('order_id');
        }
        $request->session()->put('order_id', $order_id);
        //delete shipment2
        if ($request->input('split_shipment') == '0') {
            if (!empty($request->input('shipment_id2'))) {
                $sub_count = $request->input('count2');
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input("shipment_detail2_" . $sub_cnt))) {
                        $shipment_detail_id = $request->input("shipment_detail2_" . $sub_cnt);
                        Listing_service_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Prep_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Product_labels_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Supplier_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Shipment_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                    }
                }
                Shipments::where('shipment_id', $request->input('shipment_id2'))->delete();
            }
        }
        for ($cnt = 1; $cnt <= $request->input('ship_count'); $cnt++) {
            //update shipment and shipment detail
            if (!empty($request->input('shipment_id' . $cnt))) {
                $shipment = array('order_id' => $order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                Shipments::where('shipment_id', $request->input('shipment_id' . $cnt))->update($shipment);
                $sub_count = $request->input('count' . $cnt);
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
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
                        Shipment_detail::where('shipment_detail_id', $request->input("shipment_detail" . $cnt . "_" . $sub_cnt))->update($shipment_details);
                    } //new product add in current shipment
                    else {
                        if (!empty($request->input('product_desc' . $cnt . "_" . $sub_cnt))) {
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
                        }
                    }
                }
            } //insert shipment and shipment detail
            else {
                $shipment = array('order_id' => $order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                $shipment = new Shipments($shipment);
                $shipment->save();
                $last_id = $shipment->shipment_id;
                $sub_count = $request->input('count' . $cnt);
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input('product_desc' . $cnt . "_" . $sub_cnt))) {
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
                    }
                }
            }
        }
        $order_detail = array('steps' => '1');
        Order::where('order_id', $order_id)->update($order_detail);
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
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
    public function edit(Request $request, $id)
    {
        if (!empty($id)) {
            $request->session()->put('order_id', $id);
            $steps = Order::where('order_id', $id)->get();
            if ($steps[0]->steps == 2)
                return redirect('order/supplierdetail');
            else if ($steps[0]->steps == 3)
                return redirect('order/preinspection');
            else if ($steps[0]->steps == 4)
                return redirect('order/productlabels');
            else if ($steps[0]->steps == 5)
                return redirect('order/prepservice');
            else if ($steps[0]->steps == 6)
                return redirect('order/listservice');
            else if ($steps[0]->steps == 7)
                return redirect('order/outbondshipping');
            else if ($steps[0]->steps == 8)
                return redirect('order/reviewshipment');
            else if ($steps[0]->steps == 9)
                return redirect('payment');
        }
        $user = \Auth::user();
        $order_id = $request->session()->get('order_id');
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment = Shipments::where('order_id', $order_id)->get();
        $shipment_detail = Shipment_detail::selectRaw("shipment_details.* ")
            ->join('shipments', 'shipments.shipment_id', '=', 'shipment_details.shipment_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->where('shipments.is_activated', '0')
            ->get();
        return view('shipment.shipment')->with(compact('shipping_method', 'product', 'shipment', 'shipment_detail'));
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
        //create order
        if (empty($request->input('order_id'))) {
            $order_detail = array('order_no' => Uuid::generate(1, time())->string,
                'user_id' => $user->id
            );
            $order = new Order($order_detail);
            $order->save();
            $order_id = $order->order_id;
        } else {
            $order_id = $request->input('order_id');
        }
        $request->session()->put('order_id', $order_id);
        //delete shipment2
        if ($request->input('split_shipment') == '0') {
            if (!empty($request->input('shipment_id2'))) {
                $sub_count = $request->input('count2');
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input("shipment_detail2_" . $sub_cnt))) {
                        $shipment_detail_id = $request->input("shipment_detail2_" . $sub_cnt);
                        Listing_service_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Prep_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Product_labels_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Supplier_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                        Shipment_detail::where('shipment_detail_id', $shipment_detail_id)->delete();
                    }
                }
                Shipments::where('shipment_id', $request->input('shipment_id2'))->delete();
            }
        }
        for ($cnt = 1; $cnt <= $request->input('ship_count'); $cnt++) {
            //update shipment and shipment detail
            if (!empty($request->input('shipment_id' . $cnt))) {
                $shipment = array('order_id' => $order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                Shipments::where('shipment_id', $request->input('shipment_id' . $cnt))->update($shipment);
                $sub_count = $request->input('count' . $cnt);
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
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
                        Shipment_detail::where('shipment_detail_id', $request->input("shipment_detail" . $cnt . "_" . $sub_cnt))->update($shipment_details);
                    } //new product add in current shipment
                    else {
                        if (!empty($request->input('product_desc' . $cnt . "_" . $sub_cnt))) {
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
                        }
                    }
                }
            } //insert shipment and shipment detail
            else {
                $shipment = array('order_id' => $order_id,
                    'shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                $shipment = new Shipments($shipment);
                $shipment->save();
                $last_id = $shipment->shipment_id;
                $sub_count = $request->input('count' . $cnt);
                for ($sub_cnt = 1; $sub_cnt <= $sub_count; $sub_cnt++) {
                    if (!empty($request->input('product_desc' . $cnt . "_" . $sub_cnt))) {
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
                    }
                }
            }
        }
        $order_detail = array('steps' => '1');
        Order::where('order_id', $order_id)->update($order_detail);
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
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
