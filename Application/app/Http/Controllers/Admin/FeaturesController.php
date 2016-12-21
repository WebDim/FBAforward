<?php

namespace App\Http\Controllers\Admin;

use App\Feature;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\FeatureRequest;
use App\Http\Controllers\Controller;

class FeaturesController extends Controller
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
        return view('admin.features.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.features.create_edit');
    }

    /**
     * @param FeatureRequest $request
     * @return mixed
     */
    public function store(FeatureRequest $request)
    {
        $feature = Feature::create($request->except('_token', 'feature_id'));

        $feature->save();

        return redirect('admin/features')->with('success', 'New Feature Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Feature $feature
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(Feature $feature)
    {
        return $feature;
    }

    /**
     * @param Feature $feature
     * @return mixed
     */
    public function edit(Feature $feature)
    {
        return view('admin.features.create_edit')->with(compact('feature'));
    }

    /**
     * @param FeatureRequest $request
     * @param Feature $feature
     * @return mixed
     */
    public function update(FeatureRequest $request, Feature $feature)
    {
        $feature->name = $request->input('name');

        $feature->status = $request->input('status');

        $feature->setUpdatedAt(\Carbon::now());

        $feature->save();

        return redirect('admin/features')->with('success', 'Feature updated Successfully');
    }

    /**
     * @param Request $request
     * @param Feature $feature
     * @return string
     * @throws \Exception
     */
    public function destroy(Request $request, Feature $feature)
    {
        if ($request->ajax()) {
            $feature->delete();
            return response()->json(['success' => 'Package has been deleted successfully']);
        } else {
            return 'You can\'t proceed in delete operation';
        }
    }
}
