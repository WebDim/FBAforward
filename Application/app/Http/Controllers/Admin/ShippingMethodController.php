<?php

namespace App\Http\Controllers\Admin;

use App\Shipping_method;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ShippingMethodRequest;
use App\Http\Controllers\Controller;
class ShippingMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.ShippingMethod.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ShippingMethod.create_edit');
    }

    /**
     * @param PackageRequest $request
     * @return mixed
     */
    public function store(ShippingMethodRequest $request)
    {
        $method = new Shipping_method();
        $method->shipping_name = $request->input('name');
        $method->port_fee = $request->input('port_fee');
        $method->custom_brokrage = $request->input('custom_brokrage');
        $method->consulting_fee  = $request->input('consulting_fee');
        $method->save();
       //$package->features()->sync($features);
       return redirect('admin/shippingmethod')->with('success', $method->shipping_name . ' Shipping Method Added Successfully');
    }


    /**
     * @param Package $package
     * @return mixed
     */
    public function edit(Shipping_method $shipping_method)
    {
         return view('admin.ShippingMethod.create_edit')->with(compact('shipping_method'));
    }

    /**
     * @param PackageRequest $request
     * @param Package $package
     * @return mixed
     */
    public function update(ShippingMethodRequest $request, Shipping_method $method)
    {

        $method->shipping_name = $request->input('name');
        $method->port_fee = $request->input('port_fee');
        $method->custom_brokrage = $request->input('custom_brokrage');
        $method->consulting_fee  = $request->input('consulting_fee');
        $method->save();
        return redirect('admin/shippingmethod')->with('success', $method->shipping_name . ' Shipping Method Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Package $package
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(Request $request, Shipping_method $method)
    {
        if ($request->ajax()) {

              $method->delete();

            return response()->json(['success' => 'Shipping Method has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
