<?php

namespace App\Http\Controllers\Admin;

use App\Addresses;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\AddressesRequest;
use App\Http\Controllers\Controller;
class AddressesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.Addresses.index');
    }

    public function create()
    {
        return view('admin.Addresses.create_edit');
    }

    public function store(AddressesRequest $request)
    {
        $address = new Addresses();
        $address->type = $request->input('type');
        $address->address_1 = $request->input('address1');
        $address->address_2 = $request->input('address2');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->postal_code = $request->input('postal_code');
        $address->country = $request->input('country');
        $address->save();
        return redirect('admin/addresses')->with('success', $address->type . ' Addresses Added Successfully');
    }

    public function edit(Addresses $address)
    {
        return view('admin.Addresses.create_edit')->with(compact('address'));
    }

    public function update(AddressesRequest $request, Addresses $address)
    {
        $address->type = $request->input('type');
        $address->address_1 = $request->input('address1');
        $address->address_2 = $request->input('address2');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->postal_code = $request->input('postal_code');
        $address->country = $request->input('country');
        $address->save();
        return redirect('admin/addresses')->with('success', $address->type . ' Addresses Updated Successfully');
    }

    public function destroy(Request $request, Addresses $address)
    {
        if ($request->ajax()) {
            $address->delete();
            return response()->json(['success' => 'Addresses has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
