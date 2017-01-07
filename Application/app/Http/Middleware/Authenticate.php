<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Customer_amazon_detail;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            else {
                return redirect()->guest('login');
            }
        }
        if(Auth::user()->role->name == 'Customer')
        {
            $user = Auth::user();
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
            if($customer_amazon_detail[0]->mws_seller_id=='')
            {
                echo "test";
                exit;
            }

        }

        return $next($request);
    }
}
