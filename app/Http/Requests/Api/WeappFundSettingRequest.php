<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class WeappFundSettingRequest extends FormRequest
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
        return [
            'openid' => 'required|string',
            'salary' => 'required|string',
            'saving' => 'required|string',
            'basemoney' => 'required|string',
        ];
    }
}
