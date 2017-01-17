<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\OutboundMethodRequest;
use App\Http\Controllers\Controller;
use App\Outbound_method;
class OutboundMethodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.OutboundMethod.index');
    }
    public function create()
    {
        return view('admin.OutboundMethod.create_edit');
    }
    public function store(OutboundMethodRequest $request,Outbound_method $outbound_method)
    {
       $outbound_method->outbound_name = $request->input('outbound_name');
       $outbound_method->save();
       return redirect('admin/outboundmethod')->with('success', $outbound_method->outbound_name . ' Prep Service Added Successfully');
    }
    public function edit(Outbound_method $outbound_method)
    {
       return view('admin.OutboundMethod.create_edit')->with(compact('outbound_method'));
    }
    public function update(OutboundMethodRequest $request, Outbound_method $method)
    {
        $method->outbound_name = $request->input('outbound_name');
        $method->save();
        return redirect('admin/outboundmethod')->with('success', $method->outbound_name . ' Shipping Method Updated Successfully');
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
    public function destroy(OutboundMethodRequest $request, Outbound_method $method)
    {
        if ($request->ajax()) {

            $method->delete();

            return response()->json(['success' => 'Outbound Method has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}