<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ListingServiceRequest extends Request {

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
                    'service_name' => 'required',
                    'price' => 'required',

                    'description' =>'required'
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
            'service_name' => 'required',
            'price' => 'required',

            'description' =>'required'
        ];
    }

}
