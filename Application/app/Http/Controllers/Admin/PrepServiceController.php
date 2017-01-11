<?php

namespace App\Http\Controllers\Admin;

use App\Prep_service;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PrepServiceRequest;
use App\Http\Controllers\Controller;
class PrepServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.Prepservice.index');
    }
    public function create()
    {
        return view('admin.Prepservice.create_edit');
    }
    public function store(PrepServiceRequest $request)
    {
        $prep_service = new Prep_service();
        $prep_service->service_name = $request->input('service_name');
        $prep_service->price = $request->input('price');
        $prep_service->save();
        return redirect('admin/prepservices')->with('success', $prep_service->service_name . ' Prep Service Added Successfully');
    }
    public function edit(Prep_service $prep_service)
    {
        return view('admin.Prepservice.create_edit')->with(compact('prep_service'));
    }
    public function update(PrepServiceRequest $request, Prep_service $prep_service)
    {
        $prep_service->service_name = $request->input('service_name');
        $prep_service->price = $request->input('price');
        $prep_service->save();
        return redirect('admin/prepservices')->with('success', $prep_service->service_name . ' Prep Service Updated Successfully');
    }
    public function destroy(Request $request, Prep_service $prep_service)
    {
        if ($request->ajax()) {
            $prep_service->delete();
            return response()->json(['success' => 'Prep Service has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
