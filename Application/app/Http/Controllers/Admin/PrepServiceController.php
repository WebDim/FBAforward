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
        $image = 'avatar.png';
        if ($request->hasFile('service_image')) {
            $destinationPath = public_path() . '/uploads/services';
            $image = hash('sha256', mt_rand()) . '.' . $request->file('service_image')->getClientOriginalExtension();
            $request->file('service_image')->move($destinationPath, $image);
            //\Image::make(asset('uploads/services/' . $image))->fit(300, null, null, 'top-left')->save('uploads/services/' . $image);
        }
        $prep_service = new Prep_service();
        $prep_service->service_name = $request->input('service_name');
        $prep_service->price = $request->input('price');
        $prep_service->service_image=$image;
        $prep_service->description=$request->input('description');
        $prep_service->important_information=$request->input('important_info');
        $prep_service->save();
        return redirect('admin/prepservices')->with('success', $prep_service->service_name . ' Prep Service Added Successfully');
    }
    public function edit(Prep_service $prep_service)
    {
        return view('admin.Prepservice.create_edit')->with(compact('prep_service'));
    }
    public function update(PrepServiceRequest $request, Prep_service $prep_service)
    {

       if ($request->hasFile('service_image')) {
            $destinationPath = public_path() . '/uploads/services';
            if ($prep_service->service_image != "uploads/services/avatar.png") {
                @unlink($prep_service->service_image);
            }
            $image = hash('sha256', mt_rand()) . '.' . $request->file('service_image')->getClientOriginalExtension();
            $request->file('service_image')->move($destinationPath, $image);
            \Image::make(asset('uploads/services/' . $image))->fit(300, null, null, 'top-left')->save('uploads/services/' . $image);
            $prep_service->service_image = $image;
       }
        $prep_service->service_name = $request->input('service_name');
        $prep_service->price = $request->input('price');
        $prep_service->description=$request->input('description');
        $prep_service->important_information=$request->input('important_info');
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
