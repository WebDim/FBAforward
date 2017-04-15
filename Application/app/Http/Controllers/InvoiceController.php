<?php

namespace App\Http\Controllers;


use App\Invoice_detail;
use Illuminate\Http\Request;
use App\Libraries;
use Yajra\Datatables\Datatables;
use App\Http\Middleware\Amazoncredential;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', Amazoncredential::class]);
    }
    public function index()
    {
        //
        $title = "Invoice Report";
        return view('invoice.getinvoices')->with(compact('title'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function get_ajax_invoice_detail(Request $request)
    {
        $post = $request->all();
        $start_date = $post['start_date'];
        $end_date = $post['end_date'];
        $doc_number = $post['doc_number'];
        $customer_name = $post['customer_name'];
        if ($start_date == '' && $end_date == '' && $doc_number == '' && $customer_name == '') {
            $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                ->orderby('invoice_details.created_time','desc')
                ->get();

        } else if ($start_date != '' && $end_date != '' && $doc_number != '' && $customer_name != '') {
            $end_date = $end_date . "T23:59:59";
            $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                ->where('invoice_details.created_time', '>=', date('Y-m-d', strtotime($start_date)))
                ->where('invoice_details.created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))
                ->where('invoice_details.docnumber', '=', $doc_number)
                ->Where('invoice_details.customer_ref_name', '=', $customer_name)
                ->orderby('invoice_details.created_time','desc')
                ->get();
        } else {
            $end_date = $end_date . "T23:59:59";
            if ($start_date != '' && $end_date != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->where('invoice_details.created_time', '>=', date('Y-m-d', strtotime($start_date)))
                    ->where('invoice_details.created_time', '<=', date('Y-m-dTh:i:s', strtotime($end_date)))
                    ->orderby('invoice_details.created_time','desc')
                    ->get();
            if ($doc_number != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->orWhere('invoice_details.docnumber', '=', $doc_number)
                    ->orderby('invoice_details.created_time','desc')
                    ->get();
            if ($customer_name != '')
                $invoice_details = Invoice_detail::selectRaw('orders.order_id,invoice_details.*')
                    ->join('orders', 'orders.invoice_id', '=', 'invoice_details.invoice_id', 'left')
                    ->orWhere('invoice_details.customer_ref_name', '=', $customer_name)
                    ->orderby('invoice_details.created_time','desc')
                    ->get();
        }

        return Datatables::of($invoice_details)
            ->editColumn('invoice_id', function ($invoice_detail) {
                return $invoice_detail->invoice_id;
            })
            ->editColumn('order_no', function ($invoice_detail) {
                if ($invoice_detail->order_id != '')
                    return "ORD_" . $invoice_detail->order_id;
                else
                    return "";
            })
            ->editColumn('synctoken', function ($invoice_detail) {
                return $invoice_detail->synctoken;
            })
            ->editColumn('created_time', function ($invoice_detail) {
                return $invoice_detail->created_time;
            })
            ->editColumn('updated_time', function ($invoice_detail) {
                return $invoice_detail->updated_time;
            })
            ->editColumn('docnumber', function ($invoice_detail) {
                return $invoice_detail->docnumber;
            })
            ->editColumn('txndate', function ($invoice_detail) {
                return $invoice_detail->txndate;
            })
            ->editColumn('customer_ref_name', function ($invoice_detail) {
                return $invoice_detail->customer_ref_name;
            })
            ->editColumn('line1', function ($invoice_detail) {
                return $invoice_detail->line1;//." ".$invoice_detail->line2." ".$invoice_detail->city." ".$invoice_detail->country." ".$invoice_detail->postalcode;
            })
            ->editColumn('lat', function ($invoice_detail) {
                return $invoice_detail->lat;
            })
            ->editColumn('due_date', function ($invoice_detail) {
                return $invoice_detail->due_date;
            })
            ->editColumn('total_amt', function ($invoice_detail) {
                return $invoice_detail->total_amt;
            })
            ->editColumn('currancy_ref_name', function ($invoice_detail) {
                return $invoice_detail->currancy_ref_name;
            })
            ->editColumn('total_taxe', function ($invoice_detail) {
                return $invoice_detail->total_taxe;
            })
            ->make(true);
    }
}
