<?php

namespace App\Http\Controllers;

use App\Customer_amazon_detail;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AmazoncredentialRequest;
use App\Package;
use App\Feature;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\User_info;
class MemberController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
		if(!\Auth::guest())
		{
			if (\Auth::user()->package_id != getSetting('DEFAULT_PACKAGE_ID') && \Auth::user()->package_id != 0 && !\Auth::user()->subscribed('MEMBERSHIP')) {
				Session::put('warning', 'Your Subscription not valid!');
			}else
			{
				Session::forget('warning');
			}
		}
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->role->name == 'Admin') {
            return redirect('admin/dashboard');
        }
        return view('member.home');
    }

    public function pricing()
    {
        $packages = Package::active()->get();

        $features = Feature::active()->get();

        return view('member.pricing')->with(compact('packages', 'features'));
    }

    public function profile()
    {
        $user = \Auth::user();
        $user_info = DB::table('user_infos')->where('user_id', $user->id)->get();

        return view('member.profile')->with(compact('user','user_info'));
    }

    public function editProfile()
    {
        $user = \Auth::user();
        //$job_titles = getSetting('JOB_TITLES');
        $user_info = DB::table('user_infos')->where('user_id', $user->id)->get();
        return view('member.edit_profile')->with(compact('user', 'user_info'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = \Auth::user();
        $user->name = $request->input('name');
        /* $user->mobile = $request->input('mobile');
         $user->address = $request->input('address');
         $user->job_title = $request->input('job_title');*/
        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        if ($request->hasFile('avatar')) {
            $destinationPath = public_path() . '/uploads/avatars';
            if ($user->avatar != "uploads/avatars/avatar.png") {
                @unlink($user->avatar);
            }
            $avatar = hash('sha256', mt_rand()) . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move($destinationPath, $avatar);
            \Image::make(asset('uploads/avatars/' . $avatar))->fit(300, null, null, 'top-left')->save('uploads/avatars/' . $avatar);
            $user->avatar = $avatar;
        }
        $user->save();
        $user_info= array(
            'company_name' => $request->input('company_name'),
            'company_phone' => $request->input('company_phone'),
            'company_address' => $request->input('company_address'),
            'company_address2' => $request->input('company_address2'),
            'primary_bussiness_type' => $request->input('business_type'),
            'contact_fname' => $request->input('contact_fname'),
            'contact_lname' => $request->input('contact_lname'),
        );
        User_info::where("user_id", "=", $user->id)->update($user_info);
        return redirect('member/profile')->with('success', 'Your Profile Updated Successfully');
    }
    public function amazoncredential()
    {
        $user = \Auth::user();
        $marketplace = DB::table('amazon_marketplaces')->get();
        return view('member.amazon_credential')->with(compact('user','marketplace'));
    }
   public function addamazoncredential(AmazoncredentialRequest $request)
    {
        $user = \Auth::user();
        $credentail=array(
            'user_id' =>$user->id,
            'mws_seller_id'=>$request->input('mws_seller_id'),
            'mws_market_place_id'=>$request->input('mws_market_place_id'),
            'mws_authtoken'=>$request->input('mws_authtoken'),
        );

        $credentail = new Customer_amazon_detail($credentail);
        $credentail->save();
        return redirect('member/amazon_credential')->with('success', 'Your Amazon Credential Added Successfully');
    }
}
