<?php
/**
 * Created by PhpStorm.
 * User: Webdimensions 3
 * Date: 1/4/2017
 * Time: 5:46 PM
 */
namespace App\Http\Controllers;

use App\Amazon_marketplace;
use App\Amazon_inventory;
use Illuminate\Support\Facades\DB;
use App\Libraries;
use Carbon\Carbon;
class AmazoninventoryController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {

       $results = Amazon_marketplace::selectRaw("customer_amazon_details.mws_seller_id, customer_amazon_details.user_id, customer_amazon_details.mws_authtoken, amazon_marketplaces.market_place_id")
            ->join('customer_amazon_details', 'customer_amazon_details.mws_market_place_id', '=', 'amazon_marketplaces.id')
            ->get();
        foreach ($results as $seller_detail)
        {
            $this->amazonAccountValidation($seller_detail);
        }
    }
    public function amazonAccountValidation($account)
    {
        $UserCredentials['mws_authtoken'] = $account->mws_authtoken;
        $UserCredentials['mws_seller_id'] = $account->mws_seller_id;
        //                Check User AWS Details
        $this->operation = 'ListInventorySupply';
        $this->from_date_time = "2016-12-01T07:43:29Z";
        $this->to_date_time = null;
        $service = $this->getReportsClient();
        $request = new \FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
        $request->setSellerId($account->mws_seller_id);
        $request->setQueryStartDateTime($this->from_date_time);
        $request->setMWSAuthToken($account->mws_authtoken);
        if($request->SellerId != '') {
            $arr_response = $this->invokeListInventorySupply($service, $request);
            foreach ($arr_response as $new_response) {
                foreach ($new_response->InventorySupplyList as $inventory_supply) {
                    foreach ($inventory_supply as $item) {
                        $data = array("user_id" => $account->user_id,
                            "condition" => $item->Condition,
                            "total_Supply_quantity" => $item->TotalSupplyQuantity,
                            "FNSKU" => $item->FNSKU,
                            "instock_supply_quantity" => $item->InStockSupplyQuantity,
                            "ASIN" => $item->ASIN,
                            "sellerSKU" => $item->SellerSKU
                        );
                        $get_inventory = Amazon_inventory::where('user_id', $account->user_id)->where('FNSKU', $item->FNSKU)->where('ASIN', $item->ASIN)->where('sellerSKU', $item->SellerSKU)->get();
                        if (count($get_inventory) > 0) {
                            Amazon_inventory::where('id', '=', $get_inventory[0]->id)->update($data);
                            echo "Data updated";
                        } else {
                            $inventory = new Amazon_inventory($data);
                            $inventory->save();
                            echo "Data inserted";
                        }
                    }
                }
            }
        }
        else
        {
            echo "Wrong Sellerid Passed";
        }
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

    protected function getReportsClient()
    {
        list($access_key, $secret_key, $config) = $this->getKeys();
        return new  \FBAInventoryServiceMWS_Client(
            $access_key,
            $secret_key,
            $config,
            env('APPLICATION_NAME'),
            env('APPLICATION_VERSION')
        );
    }

    public static function getMWSConfig()
    {
        return [
            'ServiceURL' => "https://mws.amazonservices.com/FulfillmentInventory/2010-10-01",
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        ];
    }

    function invokeListInventorySupply(\FBAInventoryServiceMWS_Interface $service, $request)
    {
        try {

               $response = $service->ListInventorySupply($request);
               //echo ("Service Response\n");
               //echo ("=============================================================================\n");
               $dom = new \DOMDocument();
               $dom->loadXML($response->toXML());
               $dom->preserveWhiteSpace = false;
               $dom->formatOutput = true;
               $dom->saveXML();
               //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
               $arr_response = new \SimpleXMLElement($dom->saveXML());

               return $arr_response = new \SimpleXMLElement($dom->saveXML());


        } catch (\FBAInventoryServiceMWS_Exception $ex) {
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
