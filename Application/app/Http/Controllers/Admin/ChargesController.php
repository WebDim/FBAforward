<?php
namespace App\Http\Controllers\Admin;

use App\Charges;
use App\Http\Requests\ChargesRequest;
use App\Http\Controllers\Controller;
class ChargesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.Charges.index');
    }
    public function create()
    {
        return view('admin.Charges.create_edit');
    }
    public function store(ChargesRequest $request,Charges $charges)
    {
        $charges->name = $request->input('name');
        $charges->price = $request->input('price');
        $charges->save();
        return redirect('admin/charges')->with('success', $charges->name . ' charges Added Successfully');
    }
    public function edit(Charges $charges)
    {
        return view('admin.Charges.create_edit')->with(compact('charges'));
    }
    public function update(ChargesRequest $request, Charges $charges)
    {
        $charges->name = $request->input('name');
        $charges->price = $request->input('price');
        $charges->save();
        return redirect('admin/charges')->with('success', $charges->name . ' Charges Updated Successfully');
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
    public function destroy(ChargesRequest $request, Charges $charges)
    {
        if ($request->ajax()) {

            $charges->delete();

            return response()->json(['success' => 'Charges has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}