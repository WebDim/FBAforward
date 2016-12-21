<?php

namespace App\Http\Controllers\Admin;

use App\Feature;
use App\Package;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PackageRequest;
use App\Http\Controllers\Controller;

class PackagesController extends Controller
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
        return view('admin.packages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $features = Feature::all();

        return view('admin.packages.create_edit')->with(compact('features'));
    }

    /**
     * @param PackageRequest $request
     * @return mixed
     */
    public function store(PackageRequest $request)
    {
        $package = new Package();

        $package->name = $request->input('name');

        $package->cost = $request->input('cost');

        $package->cost_per = $request->input('cost_per');

        $package->plan = $request->input('plan');

        $package->status = is_null($request->input('status')) ? 0 : 1;

        $package->featured = is_null($request->input('featured')) ? 0 : 1;

        $package->pricing_order = $request->input('pricing_order');

        $features = $request->input('features');

        if (!$features) {
            $features = [];
        }

        $package->save();

        foreach ($features as $feature) {
            $package->features()->attach($feature, ['spec' => $request->input('feature_' . $feature)]);
        }
        //$package->features()->sync($features);

        $package->save();

        return redirect('admin/packages')->with('success', $package->name . ' Package Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Package $package
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(Package $package)
    {
        return view('admin.packages.show')->with(compact('package'));
    }

    /**
     * @param Package $package
     * @return mixed
     */
    public function edit(Package $package)
    {
        $features = Feature::all();

        return view('admin.packages.create_edit')->with(compact('package', 'features'));
    }

    /**
     * @param PackageRequest $request
     * @param Package $package
     * @return mixed
     */
    public function update(PackageRequest $request, Package $package)
    {
        $package->name = $request->input('name');
        $package->cost = $request->input('cost');
        $package->cost_per = $request->input('cost_per');
        $package->plan = $request->input('plan');
        $package->status = $request->input('status');
        $package->featured = $request->input('featured');
        $package->pricing_order = $request->input('pricing_order');

        $features = $request->input('features');

        if (!$features) {
            $features = [];
        }

        $package->features()->detach();

        foreach ($features as $feature) {
            $package->features()->attach($feature, ['spec' => $request->input('feature_' . $feature)]);
        }

//        $package->features()->sync($features);

        $package->save();

        return redirect('admin/packages')->with('success', $package->name . ' Package updated Successfully');
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
    public function destroy(Request $request, Package $package)
    {
        if ($request->ajax()) {

            $package->features()->detach();

            $package->delete();

            return response()->json(['success' => 'Package has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
