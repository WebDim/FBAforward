<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditcardRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\Request;
use App\Package;
use App\Feature;
use App\Amazon_inventory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\User_info;
use PayPal\Api\CreditCard;
use App\User_credit_cardinfo;
class MemberController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {

		/*if(!\Auth::guest())
		{
			if (\Auth::user()->package_id != getSetting('DEFAULT_PACKAGE_ID') && \Auth::user()->package_id != 0 && !\Auth::user()->subscribed('MEMBERSHIP')) {
				Session::put('warning', 'Your Subscription not valid!');
			}else
			{
				Session::forget('warning');
			}
		}*/

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
        return view('member.home');
    }

   /* public function pricing()
    {
        $packages = Package::active()->get();

        $features = Feature::active()->get();

        return view('member.pricing')->with(compact('packages', 'features'));
    }*/

    public function profile()
    {
        $user = \Auth::user();
        if (\Auth::user()->role->name == 'Admin') {
            return redirect('admin/users/'.$user->id);
        }
        $user_info = DB::table('user_infos')->where('user_id', $user->id)->get();
        if(\Auth::user()->role->name == 'Customer')
        {
            $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            if(empty($customer_amazon_detail))
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');
            }
            elseif ($customer_amazon_detail[0]->mws_seller_id=='')
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');
            }
        }
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
            //\Image::make(asset('uploads/avatars/' . $avatar))->fit(300, null, null, 'top-left')->save('uploads/avatars/' . $avatar);
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
    public function amazoninventorylist()
    {
        $user = \Auth::user();
        if(\Auth::user()->role->name == 'Customer')
        {
            $customer_amazon_detail = DB::table('customer_amazon_details')->where('user_id','=',$user->id)->get();
            if(empty($customer_amazon_detail))
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');
            }
            elseif ($customer_amazon_detail[0]->mws_seller_id=='')
            {
                return redirect('amazon_credential')->with('error', 'Your Amazon Credential must be fill up');
            }
        }
        $inventory_list = Amazon_inventory::where('user_id', $user->id)->get();
        return view('member.amazon_inventory_list')->with(compact('user', 'inventory_list'));
    }
    public function creditcarddetail()
    {
        $card_type= array('visa'=>'visa',
                        'mastercard'=>'mastercard',
                        'amex'=>'amex',
                        'discover'=>'discover',
                        'maestro'=>'maestro'
                        );
        return view('member.creditcard_detail')->with(compact('card_type'));
    }
    public function addcreditcarddetail(CreditcardRequest $request)
    {
       $user = \Auth::user();
       $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'ATZYtBR5Q78IyeyfBqznRDn-u5cOmbQ4I-F7SliUlBZnLuvJC2CG78casVBs39nzcowPQxh7UQIh9wxk',
                'EB-7iN9A54Z5f70wUQ6Guau1Wj_Kx94EuhFQveM1qlDRcAG6LmYe-MmDsH53phtBRxVhXyc4U_aOX2bz'
            )
        );

        $date = explode(' ',$request->input('expire_card'));
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
        }
        catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }
        catch (Exception $ex) {
            die($ex);
        }
        $card_detail=array('user_id'=>$user->id,
                            'credit_card_type'=>$card->type,
                            'credit_card_number'=>$card->number,
                            'credit_card_id' =>$card->id
        );
         User_credit_cardinfo::create($card_detail);
        return redirect('creditcard_detail')->with('success', 'Your credit card information successfully store on paypal vault');
    }


}
