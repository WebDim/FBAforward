<?php

namespace App\Http\Controllers\Admin;

use App\Listing_service;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ListingServiceRequest;
use App\Http\Controllers\Controller;
class ListingServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('admin.Listingservice.index');
    }
    public function create()
    {
        return view('admin.Listingservice.create_edit');
    }
    public function store(ListingServiceRequest $request)
    {
        $image = 'avatar.png';
        if ($request->hasFile('service_image')) {
            $destinationPath = public_path() . '/uploads/services';
            $image = hash('sha256', mt_rand()) . '.' . $request->file('service_image')->getClientOriginalExtension();
            $request->file('service_image')->move($destinationPath, $image);
            //\Image::make(asset('uploads/services/' . $image))->fit(300, null, null, 'top-left')->save('uploads/services/' . $image);
        }
        $list_service = new Listing_service();
        $list_service->service_name = $request->input('service_name');
        $list_service->price = $request->input('price');
        $list_service->service_image=$image;
        $list_service->description=$request->input('description');
        $list_service->important_information=$request->input('important_info');
        $list_service->save();
        return redirect('admin/listingservices')->with('success', $list_service->service_name . ' Listing Service Added Successfully');
    }
    public function edit(Listing_service $list_service)
    {
        return view('admin.Listingservice.create_edit')->with(compact('list_service'));
    }
    public function update(ListingServiceRequest $request, Listing_service $list_service)
    {
        if ($request->hasFile('service_image')) {
            $destinationPath = public_path() . '/uploads/services';
            if ($list_service->service_image != "uploads/services/avatar.png") {
                @unlink($list_service->service_image);
            }
            $image = hash('sha256', mt_rand()) . '.' . $request->file('service_image')->getClientOriginalExtension();
            $request->file('service_image')->move($destinationPath, $image);
            //\Image::make(asset('uploads/services/' . $image))->fit(300, null, null, 'top-left')->save('uploads/services/' . $image);
            $list_service->service_image = $image;
        }
        $list_service->service_name = $request->input('service_name');
        $list_service->price = $request->input('price');
        $list_service->description=$request->input('description');
        $list_service->important_information=$request->input('important_info');
        $list_service->save();
        return redirect('admin/listingservices')->with('success', $list_service->service_name . ' Listing Service Updated Successfully');
    }
    public function destroy(Request $request, Listing_service $list_service)
    {
        if ($request->ajax()) {
            $list_service->delete();
            return response()->json(['success' => 'Listing Service has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
