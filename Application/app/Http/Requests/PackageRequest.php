<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PackageRequest extends Request
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
                return [
                    'name' => 'required|max:60|unique:packages,name',
                    'plan' => 'required',
                    'cost' => 'required',
                    'cost_per' => 'required',
                    'pricing_order' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH': {
                return [
                    'name' => 'required|max:60|unique:packages,id,' . $this->input('package_id'),
                    'plan' => 'required',
                    'cost' => 'required',
                    'cost_per' => 'required',
                    'pricing_order' => 'required',
                ];
            }
            default:
                break;
        }//switch
        return [
            //
        ];
    }
}
