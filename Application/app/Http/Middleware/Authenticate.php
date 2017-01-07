<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AmazonController;
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
        $user = Auth::user();

       /* if(Auth::user()->role->name == 'Customer')
        {
            $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            if(empty($customer_amazon_detail))
            {
                //return redirect()->guest('login');
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');
            }
            elseif ($customer_amazon_detail[0]->mws_seller_id=='')
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');

            }
        }*/

        return $next($request);
    }
}
