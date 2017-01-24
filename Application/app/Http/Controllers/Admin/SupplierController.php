<?php

namespace App\Http\Controllers\Admin;

use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\SupplierRequest;
use App\Http\Controllers\Controller;
class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.Supplier.index');
    }

    public function create()
    {
        return view('admin.Supplier.create_edit');
    }

    public function store(SupplierRequest $request)
    {
        $user=\Auth::user();
        $supplier = new Supplier();
        $supplier->user_id=$user->id;
        $supplier->company_name = $request->input('company_name');
        $supplier->contact_name = $request->input('contact_name');
        $supplier->email = $request->input('email');
        $supplier->phone_number = $request->input('phone_number');
        $supplier->save();
        return redirect('admin/suppliers')->with('success', $supplier->company_name . ' Supplier Added Successfully');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.Supplier.create_edit')->with(compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->company_name = $request->input('company_name');
        $supplier->contact_name = $request->input('contact_name');
        $supplier->email = $request->input('email');
        $supplier->phone_number = $request->input('phone_number');
        $supplier->save();
        return redirect('admin/suppliers')->with('success', $supplier->company_name . ' Supplier Updated Successfully');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if ($request->ajax()) {
            $supplier->delete();
            return response()->json(['success' => 'Supplier has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
