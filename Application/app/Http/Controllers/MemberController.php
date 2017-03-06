<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditcardRequest;
use App\Http\Requests\ProfileRequest;
use App\Amazon_inventory;
use Illuminate\Support\Facades\DB;
use App\User_info;
use PayPal\Api\CreditCard;
use App\User_credit_cardinfo;
use App\Http\Middleware\Amazoncredential;
use Illuminate\Http\Request;
class MemberController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth',Amazoncredential::class]);
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
    public function profile()
    {
        $user = \Auth::user();
        if (\Auth::user()->role->name == 'Admin') {
            return redirect('admin/users/'.$user->id);
        }
        $user_info = DB::table('user_infos')->where('user_id', $user->id)->get();
        return view('member.profile')->with(compact('user','user_info'));
    }

    public function editProfile()
    {
        $user = \Auth::user();
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

        $inventory_list = Amazon_inventory::where('user_id', $user->id)->get();
        return view('member.amazon_inventory_list')->with(compact('user', 'inventory_list'));
    }
    public function addnickname(Request $request)
    {
        $id=$request->input('id');
        $nickname=$request->input('nickname');
        $list =Amazon_inventory::where('id','<>',$id)->where('product_nick_name',$nickname)->get();
       if(count($list)>0)
       {
           return redirect('member/amazoninventorylist')->with('warning','Product Nick Name Already Assign To Another Product');
       }
       else
       {
           $data=array('product_nick_name'=>$nickname);
           Amazon_inventory::where('id',$id)->update($data);
           return redirect('member/amazoninventorylist')->with('success','Product Nick Name successfully changed');
       }

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
                env('CLIENT_ID'),
                env('SECRET_KEY')
            )
        );

        $date = explode('-',$request->input('expire_card'));
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
