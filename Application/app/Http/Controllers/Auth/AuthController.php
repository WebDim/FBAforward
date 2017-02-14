<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\User_info;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use CountryState;

class AuthController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = 'member/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|min:6|confirmed',
                    //'role' => 'required',
                    'company_name' => 'required',
                    'company_phone' => 'required',
                    'company_address' => 'required',
                    'company_city' => 'required',
                    'company_state' => 'required',
                    'company_zipcode' => 'required',
                    'company_country' => 'required',
                    'tax_id_number' => 'required',
                    'business_type' => 'required',
                    'annual_amazon_revenue' => 'required',
                    'annual_order' => 'required',
                    'reference' => 'required',
                    'contact_fname' => 'required',
                    'contact_lname' => 'required',
                    'contact_email' => 'required',
                    'contact_phone' => 'required',
                    'accounts_payable' => 'required',
                    'accounts_email' => 'required',
                    'accounts_phone' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function getRegister()
    {
        return $this->showRegistrationForm();
    }
    public function showRegistrationForm()
    {
       // $roles = DB::table('roles')->whereNotIn('id', [1, 2])->get();
        //$country = \Config::get('constant.country_name');
        $country = CountryState::getCountries();
        $states = CountryState::getStates('US');
        if (property_exists($this, 'registerView')) {
            return view($this->registerView)->with(compact('country'));
        }

        return view('auth.register')->with(compact('country','states'));
    }

    /**
     * @param Request $request ajax call
     * @return $this
     */
    public function selectState(Request $request){
        if ($request->ajax()) {
            $post = $request->all();
            $states = CountryState::getStates($post['country_code']);
            return view('auth.change_state')->with(compact('states'));
        }
    }
    protected function create(array $data) {

        $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role_id' => '3',
                    'avatar' => 'avatar.png',
                    'password' => bcrypt($data['password']),


        ]);
        $insertedId = $user->id;
        User_info::create([
            'user_id' => $insertedId,
            'company_name' => $data['company_name'],
            'company_phone' => $data['company_phone'],
            'company_address' => $data['company_address'],
            'company_address2' => $data['company_address2'],
            'company_city' => $data['company_city'],
            'company_state' => $data['company_state'],
            'company_zipcode' => $data['company_zipcode'],
            'company_country' => $data['company_country'],
            'tax_id_number' => $data['tax_id_number'],
            'primary_bussiness_type' => $data['business_type'],
            'estimate_annual_amazon_revenue' => $data['annual_amazon_revenue'],
            'estimate_annual_fba_order' => $data['annual_order'],
            'reference_from' => $data['reference'],
            'contact_fname' => $data['contact_fname'],
            'contact_lname' => $data['contact_lname'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'secondary_contact_phone' => $data['secondary_contact_phone'],
            'secondary_contact_email' => $data['secondary_contact_email'],
            'account_payable' => $data['accounts_payable'],
            'account_email' => $data['accounts_email'],
            'account_phone' => $data['accounts_phone'],
        ]);
        return $user;
    }

}
