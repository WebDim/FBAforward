<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
class Amazoncredential
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        if(\Auth::user()->role->name == 'Customer')
        {
            $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            if(empty($customer_amazon_detail))
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be set for further process');
            }
            elseif ($customer_amazon_detail[0]->mws_seller_id=='')
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be set for further process');
            }
        }
        return $next($request);
    }
}
