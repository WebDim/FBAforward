<?php

namespace App\Http\Controllers;

use App\Customer_amazon_detail;
use App\Http\Requests\AmazoncredentialRequest;
use Illuminate\Support\Facades\DB;
use App\Libraries;
use Carbon\Carbon;
class AmazonController extends Controller
{
    protected $service;
    public function __construct()
    {
       $this->middleware('auth');
    }
    public function index()
    {
    }
    public function amazoncredential()
    {
        $user = \Auth::user();
        $marketplace = DB::table('amazon_marketplaces')->get();
        $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            if(empty($customer_amazon_detail))
            {
                $customer_amazon_detail = Customer_amazon_detail::create([
                    'user_id' =>$user->id,
                    'mws_seller_id'=>'',
                    'mws_market_place_id'=>'1',
                    'mws_authtoken'=>'',
                ]);
                $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            }
        return view('amazon.amazon_credential')->with(compact('user','marketplace','customer_amazon_detail'));
    }
    public function addamazoncredential(AmazoncredentialRequest $request)
    {
        $user = \Auth::user();
        $account = Customer_amazon_detail::where('user_id', '=', $user->id)->get()->first();
        $account->mws_seller_id = $request->input('mws_seller_id');
        $account->mws_authtoken = $request->input('mws_authtoken');
        $account->dev_account_id = '9534-7346-7280';
        $account->mws_market_place_id = $request->input('mws_market_place_id');
        $validamazonMWS = $this->amazonAccountValidation($account);

      if ($validamazonMWS == 1) {
            $credentail=array(
                'user_id' =>$user->id,
                'mws_seller_id'=>encrypt($request->input('mws_seller_id')),
                'mws_market_place_id'=>$request->input('mws_market_place_id'),
                'mws_authtoken'=>encrypt($request->input('mws_authtoken')),
            );
            Customer_amazon_detail::where("user_id", "=", $user->id)->update($credentail);
            return redirect('member/amazoninventorylist')->with('success', 'Your Amazon Credential Updated Successfully, We are fetching your new products, Please passionate check it after 5 minutes.');
        }
      else{
          return redirect('amazon_credential')->with('error', 'Invalid Login Credential ');
    }
    }
    public function amazonAccountValidation($account)
    {
        $UserCredentials['mws_authtoken'] = $account->mws_authtoken;
        $UserCredentials['mws_seller_id'] = $account->mws_seller_id;
        //                Check User AWS Details
        $this->report_type = '_GET_MERCHANT_LISTINGS_DATA_';
        $this->from_date_time = Carbon::parse('3 days ago');
        $this->to_date_time = null;
        $service = $this->getReportsClient();
        $request = $this->getRequest($UserCredentials);
        $response = $service->requestReport($request);
           if(is_array($response))
           {
               return $response;
           }
           else {
            return 1;
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
        return new \MarketplaceWebService_Client(
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
            'ServiceURL' => "https://mws.amazonservices.com",
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 10,
        ];
    }
    private function getRequest($UserCredentials)
    {
        $request = new \MarketplaceWebService_Model_RequestReportRequest();
        $request->setReportType($this->report_type);
        if ($this->from_date_time) {
            $request->setStartDate($this->from_date_time);
        }
        if ($this->to_date_time) {
            $request->setEndDate($this->to_date_time);
        }
        $this->initRequest($UserCredentials, $request, 'setMerchant');
         return $request;
    }
    protected function initRequest($UserCredentials, $request, $merchantMethod = 'setSellerId', $setMarketPlace = null)
    {
        $request->setMWSAuthToken($UserCredentials['mws_authtoken']);
        $request->$merchantMethod($UserCredentials['mws_seller_id']);
        if (!is_null($setMarketPlace)) {
            $request->$setMarketPlace($this->account->marketplace->mws_market_place_id);
        }
    }
}
