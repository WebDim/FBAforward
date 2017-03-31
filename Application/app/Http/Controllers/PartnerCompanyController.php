<?php

namespace App\Http\Controllers;

use App\Partner_company;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
class PartnerCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title="Manage Partners";
        $company=Partner_company::get();
        //
        return view('partnercompany.index')->with(compact('company','title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = 'Add Partner';
        return view('partnercompany.create_edit')->with(compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user=\Auth::user();
        $data = array('delivery_company'=>$request->input('delivery_company'),
                      'terminal'=>$request->input('terminal'),
                      'destination'=>$request->input('destination'),
                      'user_id'=>$user->id
                );
        Partner_company::create($data);
       return redirect('partnercompany')->with('success','Partner Create Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Partner_company $company)
    {
        //
        $title = 'Edit Partner';

        return view('partnercompany.create_edit')->with(compact('company','title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Partner_company $company)
    {
        //
        $company->delivery_company = $request->input('delivery_company');
        $company->terminal = $request->input('terminal');
        $company->destination = $request->input('destination');
        $company->save();
        return redirect('partnercompany')->with('success','Partner Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        if ($request->ajax()) {
            $post=$request->all();
            Partner_company::where('id',$post['id'])->delete();
            return 1;
        }
    }
   /* public function getcompany(Request $request)
    {

           $company = Partner_company::get();
        return Datatables::of($company)
            ->editColumn('delivery_company', function ($company) {
                return $company->delivery_company;
            })
            ->editColumn('terminal', function ($company) {
                return $company->terminal;
            })
            ->editColumn('destination', function ($company) {
                return $company->destination;
            })
            ->addColumn('actions', function ($company) {
                   $editBtn = '<a style="margin-right: 0.1em;" href="' . url('partnercompany/' . $company->id . '/edit') . '"  title="Edit"><i class="fa fa-2 fa-pencil"></i></a>';
                   $deleteBtn = '&nbsp;<a href="' . url('partnercompany/' . $company->id) . '" class="message_box text-danger" data-box="#message-box-delete" data-action="DELETE" title="Delete"><i class="fa fa-2 fa-remove"></i></i></a>';
                   $buttons = '' . $editBtn . $deleteBtn;
                return $buttons;
            })
            ->make(true);
    }*/
}
