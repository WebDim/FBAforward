<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order_note;
use App\Http\Middleware\Amazoncredential;
use App\Libraries;
use PDF;
use DNS1D;

class NotesController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Order_note::where('order_id', $_GET['order_id'])->get();
        echo json_encode($notes);
        exit;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        $user_role = $user->role_id;
        $notes = array('order_id' => $request->input('orderid'),
            'shipping_notes' => $request->input('shipping_note'),
            'prep_notes' => $request->input('prep_note')
        );
        Order_note::create($notes);
        if ($user_role == 8)
            return redirect('order/adminreview');
        else
            return redirect('order/orderlist');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $notes = array('shipping_notes' => $request->input('shipping_note'),
            'prep_notes' => $request->input('prep_note')
        );
        Order_note::where('id', $request->input('note_id'))->update($notes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order_note::where('id', $id)->delete();

    }
}
