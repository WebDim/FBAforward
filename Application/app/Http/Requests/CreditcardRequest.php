<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreditcardRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                return [];
            }
            case 'POST': {
                return [];
            }
            case 'PUT':
            case 'PATCH': {
                return ['credit_card_number' => 'required|regex:/^[0-9]{12,19}$/',
                    'credit_card_type' => 'required',
                    'expire_card' => 'required',
                    'cvv' => 'required|regex:/^[0-9]{3,4}$/',
                    'first_name' => 'required',
                    'last_name' => 'required'

                ];
            }
            default:
                break;
        }
        return [
            //
        ];
    }
}
