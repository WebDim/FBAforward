<?php
namespace App\Http\Controllers;
use App\Amazon_inventory;
use App\Listing_service;
use App\Listing_service_detail;
use App\Prep_detail;
use App\Prep_service;
use App\Product_labels;
use App\Supplier_detail;
use Request;
use App\Shipping_method;
use App\Http\Requests\ShipmentRequest;
use App\Shipment_detail;
use App\Supplier;
use App\Supplier_inspection;
use App\Product_labels_detail;
use App\Shipments;
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

    }

    public function shipment()
    {
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment='';
        return view('order.shipment')->with(compact('shipping_method', 'product','shipment'));
    }

    public function updateshipment()
    {
        $user = \Auth::user();
        $shipping_method = Shipping_method::all();
        $product = Amazon_inventory::where('user_id', $user->id)->get();
        $shipment= Shipments::where('user_id',$user->id)->where('is_activated','0')->get();
        $shipment_detail = Shipment_detail::selectRaw("shipment_details.* ")
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
            ->where('shipments.user_id',$user->id)
            ->get();

        return view('order.shipment')->with(compact('shipping_method', 'product','shipment','shipment_detail'));
    }
    public function addshipment(ShipmentRequest $request)
    {
         $user = \Auth::user();
        for ($cnt = 1; $cnt <= $request->input('ship_count'); $cnt++) {
            if(!empty($request->input('shipment_id'.$cnt)))
            {
                $shipment = array('shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                Shipments::where('shipment_id',$request->input('shipment_id'.$cnt))->update($shipment);
                $sub_count=$request->input('count'.$cnt);
                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
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
                    }
                    else
                    {
                        $product_id = explode(' ', $request->input('product_desc'.$cnt."_".$sub_cnt));
                        $shipment_details = array('shipment_id'=>$request->input('shipment_id'.$cnt),
                            'product_id' =>isset($product_id[1])?$product_id[1]:'',
                            'fnsku' => $request->input('upc_fnsku'.$cnt."_".$sub_cnt),
                            'qty_per_box' => $request->input('qty_per_case'.$cnt."_".$sub_cnt),
                            'no_boxs' => $request->input('no_of_case'.$cnt."_".$sub_cnt),
                            'total' => $request->input('total'.$cnt."_".$sub_cnt)
                        );
                        $shipment_detail = new Shipment_detail($shipment_details);
                        $shipment_detail->save();
                    }
                }

            }
            else {
                $shipment = array('shipping_method_id' => $request->input('shipping_method' . $cnt),
                    'user_id' => $user->id,
                    'split_shipment' => $request->input('split_shipment'),
                    'goods_ready_date' => date('Y-m-d H:i:s', strtotime($request->input('date'))),
                    'is_activated' => '0'
                );
                $shipment = new Shipments($shipment);
                $shipment->save();
                $last_id = $shipment->shipment_id;
                $sub_count=$request->input('count'.$cnt);
                for($sub_cnt=1;$sub_cnt<=$sub_count;$sub_cnt++) {
                    $product_id = explode(' ', $request->input('product_desc'.$cnt."_".$sub_cnt));
                    $shipment_details = array('shipment_id'=>$last_id,
                        'product_id' =>isset($product_id[1])?$product_id[1]:'',
                        'fnsku' => $request->input('upc_fnsku'.$cnt."_".$sub_cnt),
                        'qty_per_box' => $request->input('qty_per_case'.$cnt."_".$sub_cnt),
                        'no_boxs' => $request->input('no_of_case'.$cnt."_".$sub_cnt),
                        'total' => $request->input('total'.$cnt."_".$sub_cnt)
                    );
                    $shipment_detail = new Shipment_detail($shipment_details);
                    $shipment_detail->save();
                }
            }
        }
        return redirect('order/supplierdetail')->with('Success', 'Shipment Information Added Successfully');
    }
    public function supplierdetail()
    {
        $user = \Auth::user();
        $product = Shipment_detail::selectRaw("shipments.shipment_id, shipments.user_id, supplier_details.supplier_id, supplier_details.supplier_detail_id, shipment_details.product_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipments.shipment_id','=','shipment_details.shipment_id')
            ->join('supplier_details','shipment_details.product_id','=','supplier_details.product_id')
            ->where('shipments.user_id',$user->id)
            ->get();
        $supplier = Supplier::all();
        return view('order.supplier')->with(compact('product', 'supplier'));
    }
    public function addsupplierdetail(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            if(empty($request->input('supplier_detail_id'.$cnt))) {
                $supplier = array('supplier_id' => $request->input('supplier' . $cnt),
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
            }
        }
        return redirect('order/preinspection')->with('Success', 'Supplier Information Added Successfully');
    }
    public function addsupplier()
    {
        if (Request::ajax()) {
            $post = Request::all();
            $supplier = new Supplier();
            $supplier->company_name = $post['company_name'];
            $supplier->contact_name = $post['contact_name'];
            $supplier->email = $post['email'];
            $supplier->phone_number = $post['phone'];
            $supplier->save();
        }

    }
    public function preinspection()
    {
        $user = \Auth::user();
        $supplier = Supplier::selectRaw("suppliers.supplier_id, suppliers.company_name")
            ->join('supplier_details', 'supplier_details.supplier_id', '=', 'suppliers.supplier_id')
            ->where('supplier_details.user_id', $user->id)
            ->distinct('supplier_details.user_id')
            ->get();
        $product = Supplier_detail::selectRaw("supplier_details.supplier_id, supplier_details.supplier_detail_id, supplier_details.product_id, supplier_details.total_unit, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'supplier_details.product_id')
            ->where('supplier_details.user_id', $user->id)
            ->get();
        return view('order.pre_inspection')->with(compact('product', 'supplier'));
    }
    public function addpreinspection(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_count=$request->input('product_count'.$cnt);
            for($product_cnt=1; $product_cnt< $product_count; $product_cnt++) {
                $supplier = array('supplier_detail_id' => $request->input('supplier_detail_id'.$cnt."_".$product_cnt),
                    'user_id' => $user->id,
                    'is_inspection' => $request->input('inspection'.$cnt),
                    'inspection_decription' => $request->input('inspection_desc'.$cnt)
                );
                $supplier_inspection = new Supplier_inspection($supplier);
                $supplier_inspection->save();
            }
        }
        return redirect('order/productlabels')->with('Success', 'Labels Information Added Successfully');
    }
    public function labels()
    {
        $user = \Auth::user();
        $product_label= Product_labels::all();
        $product = Shipment_detail::selectRaw("shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->where('shipments.user_id', $user->id)
            ->get();
        return view('order.product_labels')->with(compact('product', 'product_label'));
    }
    public function addlabels(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $product_label = array('product_id' => $request->input('product_id'.$cnt),
                'product_label_id' => $request->input('labels'.$cnt),
                'fbsku' => $request->input('sku'.$cnt),
                'qty' => $request->input('total'.$cnt)
            );
            $product_labels_detail = new Product_labels_detail($product_label);
            $product_labels_detail->save();
        }
        return redirect('order/prepservice')->with('Success', 'Product Label Information Added Successfully');
    }
    public function prepservice()
    {
        $user = \Auth::user();
        $prep_service= Prep_service::all();
        $product = Shipment_detail::selectRaw("shipment_details.product_id, shipment_details.total, amazon_inventories.product_name, amazon_inventories.sellerSKU")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->where('shipments.user_id', $user->id)
            ->get();
        return view('order.prep_service')->with(compact('prep_service', 'product'));
    }
    public function addprepservice(ShipmentRequest $request)
    {
        $user = \Auth::user();
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service =$request->input('service'.$cnt);
            foreach ($service as $services) {
                $prep_service = array('user_id' => $user->id,
                    'product_id' => $request->input('product_id' . $cnt),
                    'total_qty' => $request->input('qty' . $cnt),
                    'prep_service_ids' => $services,
                    'prep_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                $prep_service_detail = new Prep_detail($prep_service);
                $prep_service_detail->save();
            }
        }
        return redirect('order/listservice')->with('Success', 'Prep Service Information Added Successfully');
    }
    public function listservice()
    {
        $user = \Auth::user();
        $list_service= Listing_service::all();
        $product = Shipment_detail::selectRaw("shipment_details.product_id, shipment_details.total, amazon_inventories.product_name")
            ->join('amazon_inventories', 'amazon_inventories.id', '=', 'shipment_details.product_id')
            ->join('shipments','shipment_details.shipment_id','=','shipments.shipment_id')
            ->where('shipments.user_id', $user->id)
            ->get();
      return view('order.list_service')->with(compact('list_service', 'product'));
    }
    public function addlistservice(ShipmentRequest $request)
    {
        $count = $request->input('count');
        for ($cnt = 1; $cnt < $count; $cnt++) {
            $service =$request->input('service'.$cnt);
            foreach ($service as $services) {
                $list_service = array(
                    'product_id' => $request->input('product_id' . $cnt),
                    'listing_service_ids' => $services,
                    'listing_service_total' => $request->input('total' . $cnt),
                    'grand_total' => $request->input('grand_total')
                );
                $list_service_detail = new Listing_service_detail($list_service);
                $list_service_detail->save();
            }
        }
        //return redirect('order/listservice')->with('Success', 'Product Label Information Added Successfully');
    }
}
