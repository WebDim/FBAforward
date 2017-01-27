<?php

namespace App\Http\Controllers;

use App\Customer_amazon_detail;
use App\Http\Requests\AmazoncredentialRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Amazon_marketplace;
use App\Libraries;
class CreateInboundShipmentPlanSample extends Controller
{
    protected $service;
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
    }
    public function createshipment()
    {
        $user = \Auth::user();
        $results = Amazon_marketplace::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken, amazon_marketplaces.market_place_id")
            ->join('customer_amazon_details', 'customer_amazon_details.mws_market_place_id', '=', 'amazon_marketplaces.id')
            ->where('customer_amazon_details.user_id',$user->id)
            ->get();
        foreach ($results as $seller_detail) {
            $this->shipmentplan($seller_detail);
        }
    }
    public function shipmentplan($account)
    {
        $UserCredentials['mws_authtoken'] = !empty($account->mws_authtoken) ? decrypt($account->mws_authtoken) : '';
        $UserCredentials['mws_seller_id'] = !empty($account->mws_seller_id) ? decrypt($account->mws_seller_id) : '';
        $this->operation = 'CreateInboundShipmentPlan';
        $service = $this->getReportsClient();
        $request = new \FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest();
        $request->setSellerId($UserCredentials['mws_seller_id']);
        $fromaddress= new \FBAInboundServiceMWS_Model_Address();
        $fromaddress->setName('Webdimensions');
        $fromaddress->setAddressLine1('satelite');
        $fromaddress->setCity('ahmedabad');
        $fromaddress->setCountryCode('in');
        $request->setShipFromAddress($fromaddress);
        $item= new \FBAInboundServiceMWS_Model_InboundShipmentPlanItem();
        $item->setSellerSKU('J2-GM5C-C2T1');
        $item->setQuantity('100');
        $itemlist= new \FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItemList();
        $itemlist->setmember($item);
        $request->setInboundShipmentPlanRequestItems($itemlist);

        $this->invokeCreateInboundShipmentPlan($service, $request);
    }
    protected function getReportsClient()
    {
        list($access_key, $secret_key, $config) = $this->getKeys();
        return  new \FBAInboundServiceMWS_Client(
            $access_key,
            $secret_key,
            env('APPLICATION_NAME'),
            env('APPLICATION_VERSION'),
            $config
        );
    }
    private function getKeys($uri = '')
    {
        add_to_path('Libraries');
        $devAccount = DB::table('dev_accounts')->first();
        return [
            $devAccount->access_key,
            $devAccount->secret_key,
            self::getMWSConfig()
        ];
    }
    public static function getMWSConfig()
    {
        return [
            'ServiceURL' => "https://mws.amazonservices.com/FulfillmentInboundShipment/2010-10-01",
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        ];
    }
    function invokeCreateInboundShipmentPlan(\FBAInboundServiceMWS_Interface $service, $request)
    {
        try {
            $response = $service->CreateInboundShipmentPlan($request);

             echo ("Service Response\n");
            echo ("=============================================================================\n");

            $dom = new \DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            echo $dom->saveXML();
            echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

        } catch (\FBAInboundServiceMWS_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
        }
    }
}

