<?php
/**
 * Created by PhpStorm.
 * User: Webdimensions 3
 * Date: 1/4/2017
 * Time: 5:46 PM
 */
namespace App\Http\Controllers;

use App\Customer_amazon_detail;
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

        $UserCredentials['mws_authtoken'] = !empty($account->mws_authtoken) ? decrypt($account->mws_authtoken) : '';
        $UserCredentials['mws_seller_id'] = !empty($account->mws_seller_id) ? decrypt($account->mws_seller_id) : '';
       // $UserCredentials['mws_authtoken']='test';
        //$UserCredentials['mws_seller_id']='A2YCP5D68N9M7J';
        //  Check User AWS Details
        $this->operation = 'ListInventorySupply';
        $this->from_date_time = "2016-12-01T07:43:29Z";
        $this->to_date_time = null;
        $service = $this->getReportsClient();
        $request = new \FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
        $request->setSellerId($UserCredentials['mws_seller_id']);
        $request->setMWSAuthToken($UserCredentials['mws_authtoken']);
        $request->setQueryStartDateTime($this->from_date_time);
        if($request->SellerId != '') {
            $productasin = array();
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

                        $productasin[] = $item->ASIN;

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
            $this->getProductInfo($productasin,$account);
        }
        else
        {
            echo "Wrong Sellerid Passed";
        }
     }
     private function getProductInfo($productasin=array(),$account)
     {
         $productasin = implode(", ", $productasin);
         $user_id = $account->user_id;
         // Your AWS Access Key ID, as taken from the AWS Your Account page

         $aws_access_key_id =  env('AWSACCESSKEY');
         // Your AWS Secret Key corresponding to the above ID, as taken from the AWS Your Account page
         $aws_secret_key =  env('ADVERTISINGSECRETKEY');

         // The region you are interested in
         $endpoint = env('AMAZONENDPOINT');
         $associatetag =  env('ASSOCIATETAG');

         $uri = "/onca/xml";
         $params = array(
             "Service" => "AWSECommerceService",
             "Operation" => "ItemLookup",
             "AWSAccessKeyId" => $aws_access_key_id,
             "AssociateTag" => $associatetag,
             "ItemId" => $productasin,
             "IdType" => "ASIN",
             "ResponseGroup" => "Images,ItemAttributes"
         );

         // Set current timestamp if not set
         if (!isset($params["Timestamp"])) {
             $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
         }

         // Sort the parameters by key
         ksort($params);

         $pairs = array();

         foreach ($params as $key => $value) {
             array_push($pairs, rawurlencode($key) . "=" . rawurlencode($value));
         }
         // Generate the canonical query
         $canonical_query_string = join("&", $pairs);

         // Generate the string to be signed
         $string_to_sign = "GET\n" . $endpoint . "\n" . $uri . "\n" . $canonical_query_string;

         // Generate the signature required by the Product Advertising API
         $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $aws_secret_key, true));

         // Generate the signed URL
         $request_url = 'http://' . $endpoint . $uri . '?' . $canonical_query_string . '&Signature=' . rawurlencode($signature);

         if (($response_xml_data = file_get_contents($request_url)) === false) {
             echo "Error fetching XML\n";
         } else {
             libxml_use_internal_errors(true);
             $data = simplexml_load_string($response_xml_data);
             $json = json_encode($data);
             $productdataArray = json_decode($json, TRUE);

             if (!$data) {
                 echo "Error loading XML\n";
                 foreach (libxml_get_errors() as $error) {
                     echo "\t", $error->message;
                 }
             } else {
                 $productArray = $productdataArray['Items']['Item'];

                 if(isset($productArray['ASIN']) && !empty($productArray['ASIN'])){
                     $inventory = Amazon_inventory::where('ASIN', $productArray['ASIN'])->where('user_id', $user_id)->first();
                     $inventory->product_name = $productArray['ItemAttributes']['Title'];
                     $inventory->image_path = '';
                     if (isset($productArray['ImageSets']['ImageSet'])) {
                         $productImages = $productArray['ImageSets']['ImageSet'];
                         $inventory->image_path = isset($productImages[0]["LargeImage"]["URL"]) ? $productImages[0]["LargeImage"]["URL"] : '';
                     }
                     $inventory->save();
                 }else {
                     foreach ($productArray as $productDetails) {

                         $inventory = Amazon_inventory::where('ASIN', $productDetails['ASIN'])->where('user_id', $user_id)->first();
                         $inventory->product_name = $productDetails['ItemAttributes']['Title'];
                         $inventory->image_path = '';
                         if (isset($productDetails['ImageSets']['ImageSet'])) {
                             $productImages = $productDetails['ImageSets']['ImageSet'];
                             $inventory->image_path = isset($productImages[0]["LargeImage"]["URL"]) ? $productImages[0]["LargeImage"]["URL"] : '';
                         }
                         $inventory->save();
                     }
                 }
             }
         }
         return true;
     }

    private function getKeys($uri = '')
    {
        add_to_path('Libraries');
        $devAccount = DB::table('dev_accounts')->first();
        //$accesskey='AKIAJSMUMYFXUPBXYQLA';
        //$secret_key='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';
        return [
            $devAccount->access_key,
            $devAccount->secret_key,
            //$accesskey,
            //$secret_key,
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
               //$arr_response = new \SimpleXMLElement($dom->saveXML());
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
