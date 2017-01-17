<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SupplierRequest extends Request {

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
                    'company_name' => 'required',
                    'contact_name' => 'required',
                    'email' => 'required',
                    'phone_number' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [

                ];
            }
            default:
                break;
        }

        return [
            'company_name' => 'required',
            'contact_name' => 'required',
            'email' => 'required',
            'phone_number' => 'required'
        ];
    }

}