<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                    return [];
                }
            case 'POST': {
                    return [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|unique:users',
                        'password' => 'required|confirmed|min:6',
                        'role' => 'required',
                        //'address' => 'required',
                        'avatar' => 'mimes:jpg,jpeg,png|max:500',
                        'company_name' =>'required',
                        'company_phone' => 'required',
                        'company_address' => 'required',
                        'company_city' => 'required',
                        'company_state' => 'required',
                        'company_country' => 'required',
                        'tax_id_number' => 'required',
                        'business_type' => 'required',
                        'annual_amazon_revenue' => 'required',
                        'annual_fba_order' => 'required',
                        'reference_from' => 'required',
                        'contact_fname' => 'required',
                        'contact_lname' => 'required',
                        'contact_email' => 'required|email',
                        'contact_phone' => 'required',
                        'account_payable' => 'required',
                        'account_email' => 'required|email',
                        'account_phone' => 'required'
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|unique:users,email,' . $this->input('user_id'),
                        'password' => 'confirmed|min:6',
                        'role' => 'required',
                        //'address' => 'required',
                        'avatar' => 'mimes:jpg,jpeg,png|max:500'
                    ];
                }
            default:
                break;
        }

        return [
        ];
    }

}
