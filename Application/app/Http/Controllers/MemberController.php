<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditcardRequest;
use App\Http\Requests\ProfileRequest;
use App\Amazon_inventory;
use App\Notification;
use App\Order;
use App\Payment_detail;
use App\Role;
use App\Supplier_inspection;
use App\User;
use App\Invoice_detail;
use Illuminate\Support\Facades\DB;
use App\User_info;
use PayPal\Api\CreditCard;
use App\User_credit_cardinfo;
use App\Http\Middleware\Amazoncredential;
use Illuminate\Http\Request;
use App\Shipping_quote;
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
        $user=\Auth::user();
        if (\Auth::user()->role->name == 'Admin') {
            return redirect('admin/dashboard');
        }
        $total_customer = User::where('role_id','3')->count();
        if($user->role->name=='Customer') {
            $total_order = Order::where('user_id', $user->id)->count();
            $total_payment = Payment_detail::selectRaw('sum(total_cost) as payment_count')
                             ->join('orders','orders.order_id','=','payment_details.order_id')
                             ->where('orders.user_id',$user->id)
                             ->get();
            $total_in_order= Order::where('is_activated','0')->where('user_id', $user->id)->count();
            $total_place_order= Order::where('is_activated','1')->where('user_id', $user->id)->count();
            $total_inspect_order= Order::where('is_activated','2')->where('user_id', $user->id)->count();
            $total_shipping_order= Order::where('is_activated','4')->where('user_id', $user->id)->count();
            $total_inventory=Amazon_inventory::where('user_id',$user->id)->count();
            return view('member.home')->with(compact('total_order','total_payment','total_in_order','total_place_order','total_inspect_order','total_shipping_order','total_inventory','user'));
        }
        elseif ($user->role->name=='customer service' || $user->role->name=='Sales')
        {
            $total_order = Order::count();
            $total_in_order= Order::where('is_activated','0')->count();
            $total_place_order= Order::where('is_activated','1')->count();
            $total_inspect_order= Order::where('is_activated','2')->count();
            $total_shipping_order= Order::where('is_activated','4')->count();
            return view('member.home')->with(compact('total_order','total_customer','total_in_order','total_place_order','total_inspect_order','total_shipping_order','user'));
        }
        elseif ($user->role->name=='Accounting')
        {
            $total_invoice = Invoice_detail::count();
            $total_amount = Invoice_detail::selectRaw('sum(total_amt) as amount_count')
                            ->get();
            return view('member.home')->with(compact('total_invoice','total_amount','user'));
        }
        elseif ($user->role->name=='Inspector')
        {
            $orders = Supplier_inspection::selectRaw('supplier_inspections.order_id')
                      ->join('orders','orders.order_id','=','supplier_inspections.order_id')
                      ->where('orders.is_activated','1')
                      ->where('supplier_inspections.is_inspection','1')
                      ->groupby('orders.order_id')
                      ->get();
            $order_count=0;
            foreach ($orders as $order)
            {
                $order_count++;
            }
            return view('member.home')->with(compact('order_count','user'));
        }
        elseif ($user->role->name=='Shipper')
        {
            $details = Order::selectRaw('orders.order_id, count(supplier_inspections.supplier_inspection_id) as count_id')
                ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
                ->where('orders.is_activated', '1')
                ->where('supplier_inspections.is_inspection', '0')
                ->orderBy('orders.created_at', 'desc')
                ->groupby('supplier_inspections.order_id')
                ->get();
            $counts = Order::selectRaw('orders.order_id, count(supplier_inspections.supplier_inspection_id) as count_id')
                ->join('supplier_inspections', 'supplier_inspections.order_id', '=', 'orders.order_id')
                ->where('orders.is_activated', '1')
                ->orderBy('orders.created_at', 'desc')
                ->groupby('supplier_inspections.order_id')
                ->get();
            $order_ids = array();
            foreach ($counts as $count) {
                foreach ($details as $detail) {
                    if( ($count->order_id==$detail->order_id) && ($count->count_id==$detail->count_id))
                        $order_ids[] = $detail->order_id;
                }
            }
            $shipping_id = Shipping_quote::selectRaw('shipping_quotes.*')
                ->join('orders','shipping_quotes.order_id','=','orders.order_id')
                ->where('orders.is_activated','4')
                ->where('shipping_quotes.status','0')
                ->groupby('shipping_quotes.order_id')
                ->get();
            $shipping_id1 = Shipping_quote::selectRaw('shipping_quotes.*')
                ->join('orders','shipping_quotes.order_id','=','orders.order_id')
                ->where('orders.is_activated','4')
                ->where('shipping_quotes.status','2')
                ->where('shipping_quotes.status','<>','1')
                ->groupby('shipping_quotes.order_id')
                ->get();
            foreach ($shipping_id as $shipping_ids)
            {
                $order_ids[]=$shipping_ids->order_id;
            }
            foreach ($shipping_id1 as $shipping_ids1)
            {
                $order_ids[]=$shipping_ids1->order_id;
            }
            if (!empty($order_ids))
                $shipping_quote_count = Order::where('orders.is_activated', '3')->orWhereIn('orders.order_id', $order_ids)->count();
            /*else
                $shipping_quote_count = Order::where('orders.is_activated', '3')->count();*/
            $bill_lading_count = Order::where('orders.is_activated', '6')->count();
            $pre_alert_count = Order::where('orders.is_activated', '8')->count();
            return view('member.home')->with(compact('total_customer','shipping_quote_count','bill_lading_count','pre_alert_count','user'));
        }
        elseif ($user->role->name=='Logistics')
        {
            $bill_lading_count = Order::where('orders.is_activated', '7')->count();
            $clearance_count = Order::where('orders.is_activated', '9')->count();
            $booking_count = Order::where('orders.is_activated', '10')->count();
            return view('member.home')->with(compact('bill_lading_count','clearance_count','booking_count','user'));
        }
        elseif ($user->role->name=='Warehouse Manager')
        {
            $review_count = Order::where('orders.is_activated', '14')->count();
            return view('member.home')->with(compact('review_count','user'));
        }
        elseif ($user->role->name=='Warehouse Admin')
        {
            $checkin_review_count = Order::where('orders.is_activated', '12')->count();
            $shipment_review_count = Order::where('orders.is_activated', '16')->count();
            return view('member.home')->with(compact('checkin_review_count','shipment_review_count','user'));
        }
        elseif ($user->role->name=='Warehouse Lead')
        {
            $checkin_count = Order::where('orders.is_activated', '11')->count();
            $labor_count = Order::where('orders.is_activated', '13')->count();
            $shipment_count = Order::where('orders.is_activated', '15')->count();
            return view('member.home')->with(compact('checkin_count','labor_count','shipment_count','user'));
        }
        return view('member.home')->with(compact('user'));
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
    public function getnotification(Request $request)
    {
        if($request->ajax()) {
            $post = $request->all();
            $role=$post['role'];
            $user = \Auth::user();
            if($role=='0') {
               $notifications = $user->notification()->unread()->get();
            }
            else
            {
                $role_detail = Role::find($user->role_id);
                $notifications = $role_detail->notification()->unread()->get();
            }
            $result= array('notification'=>$notifications,'role'=>$role);
            echo json_encode($result);
            exit;
        }
    }
    public function checkread(Request $request)
    {
        if($request->ajax())
            $post=$request->all();
        $role=$post['role'];
        $user = \Auth::user();
        if($role=='0') {
           $notifications = $user->notification()->unread()->get();
        }
        else
        {
            $role_detail = Role::find($user->role_id);
            $notifications = $role_detail->notification()->unread()->get();
        }
            $status=array();
            foreach ($notifications as $notification)
            {
                $status[]=$notification->id;
            }
            $data=array('is_read'=>'1');
            Notification::whereIn('id',$status)->update($data);

    }
    public function switchuser(Request $request)
    {

            $user = \Auth::user();
            if($request->session()->get('new_user')) {
                $request->session()->put('old_user', $user->id);
                \Auth::loginUsingId($request->session()->get('new_user'));
            }
            else
            {
                \Auth::loginUsingId($request->session()->get('old_user'));
                $request->session()->forget('old_user');
                $request->session()->forget('new_user');
            }
            return redirect('member/home');
    }
    public function storeuser(Request $request)
    {
        if($request->ajax())
        {
            $post=$request->all();
            $user_id=$post['user_id'];
            $request->session()->put('new_user',$user_id);
        }

    }

}
