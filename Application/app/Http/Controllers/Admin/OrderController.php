<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Shipments;
use App\Prep_service;
use App\Listing_service;
use App\Payment_detail;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        //
        return view('admin.orders.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {

        $shipment_detail = Shipments::selectRaw("shipments.shipment_id,shipments.shipping_method_id,shipping_methods.shipping_name,shipment_details.product_id, shipment_details.fnsku, shipment_details.qty_per_box, shipment_details.no_boxs, shipment_details.total,amazon_inventories.product_name, amazon_inventories.product_nick_name, supplier_details.supplier_detail_id,supplier_details.supplier_id,suppliers.company_name,supplier_inspections.inspection_decription,product_labels_details.product_label_id,product_labels.label_name,prep_details.prep_detail_id, prep_details.prep_service_total, prep_details.prep_service_ids,listing_service_details.listing_service_detail_id, listing_service_details.listing_service_total, listing_service_details.listing_service_ids,outbound_shipping_details.amazon_destination_id, outbound_shipping_details.outbound_method_id,outbound_methods.outbound_name,amazon_destinations.destination_name")
            ->join('shipping_methods', 'shipping_methods.shipping_method_id', '=', 'shipments.shipping_method_id', 'left')
            ->join('shipment_details', 'shipment_details.shipment_id', '=', 'shipments.shipment_id', 'left')
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id', 'left')
            ->join('supplier_details', 'shipment_details.shipment_detail_id', '=', 'supplier_details.shipment_detail_id', 'left')
            ->join('suppliers', 'suppliers.supplier_id', '=', 'supplier_details.supplier_id', 'left')
            ->join('supplier_inspections', 'supplier_inspections.supplier_detail_id', '=', 'supplier_details.supplier_detail_id', 'left')
            ->join('product_labels_details', 'product_labels_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('product_labels', 'product_labels.product_label_id', '=', 'product_labels_details.product_label_id', 'left')
            ->join('prep_details', 'prep_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('listing_service_details', 'listing_service_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('outbound_shipping_details', 'outbound_shipping_details.shipment_detail_id', '=', 'shipment_details.shipment_detail_id', 'left')
            ->join('outbound_methods', 'outbound_methods.outbound_method_id', '=', 'outbound_shipping_details.outbound_method_id', 'left')
            ->join('amazon_destinations', 'amazon_destinations.amazon_destination_id', '=', 'outbound_shipping_details.amazon_destination_id', 'left')
            ->where('shipments.order_id', $order->order_id)
            ->orderBy('shipments.shipment_id', 'ASC')
            ->get()->toArray();
        foreach ($shipment_detail as $key => $shipment_details) {
            //Fetch Prep services name
            $prep_service_ids = explode(",", $shipment_details['prep_service_ids']);
            $prep_services = Prep_service::selectRaw("service_name")->whereIn('prep_service_id', $prep_service_ids)->get();
            $service_name = array();
            if (count($prep_services) > 0) {
                foreach ($prep_services as $prep_service) {
                    $service_name[] = $prep_service->service_name;
                }
            }
            $shipment_detail[$key]['prep_service_name'] = implode($service_name, ",");
            //Fetch Listing services name
            $listing_service_ids = explode(",", $shipment_details['listing_service_ids']);
            $listing_services = Listing_service::selectRaw("service_name")->whereIn('listing_service_id', $listing_service_ids)->get();
            $listing_service_name = array();
            if (count($listing_services) > 0) {
                foreach ($listing_services as $listing_service) {
                    $listing_service_name[] = $listing_service->service_name;
                }
            }
            $shipment_detail[$key]['listing_service_name'] = implode($listing_service_name, ",");
        }
        // Payment Info get
        $payment_detail = Payment_detail::selectRaw('payment_details.*,user_credit_cardinfos.credit_card_number,user_credit_cardinfos.credit_card_type,user_credit_cardinfos.credit_card_id,payment_infos.transaction')
            ->join('payment_infos', 'payment_infos.payment_detail_id', '=', 'payment_details.payment_detail_id', 'left')
            ->join('user_credit_cardinfos', 'user_credit_cardinfos.id', '=', 'payment_details.user_credit_cardinfo_id', 'left')
            ->where('order_id', $order->order_id)->first();
        if (count($payment_detail) > 0)
            $payment_detail = $payment_detail->toArray();
              //
        return view('admin.orders.show')->with(compact('shipment_detail','payment_detail'));
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
        //
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
