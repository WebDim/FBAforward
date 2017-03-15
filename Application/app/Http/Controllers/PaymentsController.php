<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Listing_service_detail;
use App\Payment_info;
use App\Prep_detail;
use App\Setting;
use App\Supplier_detail;
use App\Shipping_method;
use App\Product_labels_detail;
use App\Order;
use App\Role;
use App\User_credit_cardinfo;
use App\Addresses;
use App\Http\Middleware\Amazoncredential;
use App\Payment_detail;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use App\Libraries;
use PDF;
use DNS1D;

class PaymentsController extends Controller
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
    public function index(Request $request)
    {
        $order_id = $request->session()->get('order_id');
        $pre_shipment_inspection = Setting::where('key_cd', 'Pre Shipment Inspection')->get();
        $order_detail = array('steps' => '8');
        Order::where('order_id', $order_id)->update($order_detail);
        $supplier = Supplier_detail::selectRaw('supplier_details.supplier_id, supplier_inspections.is_inspection')
            ->join('supplier_inspections', 'supplier_inspections.supplier_id', '=', 'supplier_details.supplier_id')
            ->where('supplier_inspections.order_id', $order_id)
            ->where('supplier_inspections.is_inspection', '1')
            ->groupby('supplier_details.supplier_id')->get();
        $supplier_count = count($supplier);
        $pre_shipment_inspection_value = isset($pre_shipment_inspection[0]->value) ? $pre_shipment_inspection[0]->value : '0';
        $pre_shipment_inspection_value = $pre_shipment_inspection_value * $supplier_count;
        $label = Product_labels_detail::SelectRaw('sum(price) as total')->where('order_id', $order_id)->groupby('order_id')->get();
        $prep_service = Prep_detail::selectRaw('grand_total')->where('order_id', $order_id)->groupby('order_id')->get();
        $listing_service = Listing_service_detail::selectRaw('grand_total')->where('order_id', $order_id)->groupby('order_id')->get();
        $shipment_fee = Shipping_method::selectRaw("shipments.shipment_id, shipments.shipping_method_id, shipping_methods.port_fee, shipping_methods.custom_brokrage, shipping_methods.consulting_fee")
            ->join('shipments', 'shipments.shipping_method_id', '=', 'shipping_methods.shipping_method_id', 'left')
            ->where('shipments.order_id', $order_id)
            ->get();
        $port_fee = 0;
        $custom_brokrage = 0;
        $consulting_fee = 0;
        foreach ($shipment_fee as $shipment_fees) {
            $port_fee = $port_fee + $shipment_fees->port_fee;
            $custom_brokrage = $custom_brokrage + $shipment_fees->custom_brokrage;
            $consulting_fee = $consulting_fee + $shipment_fees->consulting_fee;
        }
        $price = array('pre_shipment_inspection' => $pre_shipment_inspection_value,
            'shipping_cost' => '0',
            'port_fee' => $port_fee,
            'custom_brokerage' => $custom_brokrage,
            'custom_duty' => '0',
            'consult_charge' => $consulting_fee,
            'label_charge' => isset($label[0]->total) ? $label[0]->total : '0',
            'prep_forwarding' => isset($prep_service[0]->grand_total) ? $prep_service[0]->grand_total : '0',
            'listing_service' => isset($listing_service[0]->grand_total) ? $listing_service[0]->grand_total : '0',
            'inbound_shipping' => '0',
        );
        $card_type = array('visa' => 'visa',
            'mastercard' => 'mastercard',
            'amex' => 'amex',
            'discover' => 'discover',
            'maestro' => 'maestro'
        );
        $user = \Auth::user();
        $addresses = Addresses::where('user_id', $user->id)->where('type', 'B')->get();
        $credit_card = User_credit_cardinfo::where('user_id', $user->id)->get();
        return view('payment.payment')->with(compact('price', 'card_type', 'addresses', 'credit_card'));
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
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('CLIENT_ID'),
                env('SECRET_KEY')
            )
        );
        $date = explode('-', $request->input('expire_card'));
        $card = new CreditCard();
        $card->setType($request->input('credit_card_type'))
            ->setNumber($request->input('credit_card_number'))
            ->setExpireMonth($date[0])
            ->setExpireYear($date[1])
            ->setCvv2($request->input('cvv'))
            ->setFirstName($request->input('first_name'))
            ->setLastName($request->input('last_name'));
        try {
            $card->create($apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }
        $card_detail = array('user_id' => $user->id,
            'credit_card_type' => $card->type,
            'credit_card_number' => $card->number,
            'credit_card_id' => $card->id
        );
        User_credit_cardinfo::create($card_detail);
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
        $order_id = $request->session()->get('order_id');
        $credit_card_detail = explode(' ', $request->input('credit_card_detail'));
        $payment_detail = array('address_id' => $request->input('address'),
            'order_id' => $order_id,
            'user_credit_cardinfo_id' => isset($credit_card_detail[0]) ? $credit_card_detail[0] : '',
            'pre_shipment_inspection' => $request->input('pre_ship_inspect'),
            'shipping_cost' => $request->input('shipping_cost'),
            'port_fees' => $request->input('port_fees'),
            'customs_brokerage' => $request->input('custom_brokerage'),
            'customs_duty' => $request->input('custom_duty'),
            'consulting_charge' => $request->input('consulting'),
            'labels_charge' => $request->input('label_charge'),
            'prep_forward_charge' => $request->input('prep_forward'),
            'listing_service_charge' => $request->input('listing_service'),
            'total_fbaforward_charge' => $request->input('total_fbaforward'),
            'inbound_shipping_charge' => $request->input('inbound_shipping'),
            'total_cost' => $request->input('total_cost')
        );
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('CLIENT_ID'),
                env('SECRET_KEY')
            )
        );
        $creditCardToken = new CreditCardToken();
        $creditCardToken->setCreditCardId($credit_card_detail[1]);
        $fi = new FundingInstrument();
        $fi->setCreditCardToken($creditCardToken);
        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($request->input('total_cost'));
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));
        $request = clone $payment;
        try {
            $payment->create($apiContext);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            ResultPrinter::printError("Create Payment using Saved Card", "Payment", null, $request, $ex);
            exit(1);
        }
        $payment_detail_id = Payment_detail::create($payment_detail);
        $last_id = $payment_detail_id->payment_detail_id;
        $payment_info = array('payment_detail_id' => $last_id,
            'transaction' => $payment
        );
        Payment_info::create($payment_info);
        $order_detail = array('is_activated' => '1', 'steps' => '9');
        Order::where('order_id', $order_id)->update($order_detail);
        $details = Order::selectRaw('orders.order_id')
            ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
            ->where('orders.is_activated', '1')
            ->where('supplier_inspections.is_inspection', '0')
            ->orderBy('orders.created_at', 'desc')
            ->distinct('supplier_inspections.order_id')
            ->where('orders.order_id', $order_id)
            ->get();
        if (count($details) > 0) {
            $role = Role::find(5);
            $role->newNotification()
                ->withType('shipping quote')
                ->withSubject('You have shipping quote for upload')
                ->withBody('You have shipping quote for upload')
                ->regarding($payment_detail)
                ->deliver();
        } else {
            $role = Role::find(12);
            $role->newNotification()
                ->withType('report')
                ->withSubject('You have inspection report for upload')
                ->withBody('You have inspection report for upload')
                ->regarding($payment_detail)
                ->deliver();
        }
        return redirect('order/index')->with('success', 'Your order Successfully Placed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    //add billing address for particular user
    public function addaddress(Request $request)
    {
        if ($request->ajax()) {
            $user = \Auth::user();
            $address_detail = array('user_id' => $user->id,
                'type' => 'B',
                'address_1' => $request->input('address_line_1'),
                'address_2' => $request->input('address_line_2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => $request->input('postal_code'),
                'country' => $request->input('country')
            );
            Addresses::create($address_detail);
        }
    }

}
