<?php

namespace App\Http\Controllers;


use App\Supplier_detail;
use App\Supplier;
use App\Supplier_inspection;
use App\Order;
use App\Http\Middleware\Amazoncredential;
use Illuminate\Http\Request;
use App\Libraries;
use PDF;
use Yajra\Datatables\Datatables;
use DNS1D;

class PreinspectionController extends Controller
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
    //
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
        return view('pre_inspection.pre_inspection')->with(compact('product', 'supplier'));
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
