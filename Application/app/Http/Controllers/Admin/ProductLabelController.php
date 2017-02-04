<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductLabelRequest;
use App\Http\Controllers\Controller;
use App\Product_labels;
class ProductLabelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.Productlabel.index');
    }
    public function create()
    {
        return view('admin.Productlabel.create_edit');
    }
    public function store(ProductlabelRequest $request,Product_labels $product_label)
    {
        $product_label->label_name = $request->input('label_name');
        $product_label->Price = $request->input('price');
        $product_label->save();
        return redirect('admin/productlabel')->with('success', $product_label->label_name . ' Product Label Added Successfully');
    }
    public function edit(Product_labels $product_label)
    {
        return view('admin.Productlabel.create_edit')->with(compact('product_label'));
    }
    public function update(ProductlabelRequest $request, Product_labels $product_label)
    {
        $product_label->label_name = $request->input('label_name');
        $product_label->Price = $request->input('price');
        $product_label->save();
        return redirect('admin/productlabel')->with('success', $product_label->label_name . ' Product Label Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OutboundMethodRequest $request
     * @param Package $package
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(ProductlabelRequest $request, Product_labels $product_label)
    {
        if ($request->ajax()) {

            $product_label->delete();

            return response()->json(['success' => 'Product Label has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}