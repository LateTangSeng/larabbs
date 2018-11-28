<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class WeappFundCodeCheckRequest extends FormRequest
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
            'fundcode' => 'required|string',
            'openid' => 'required|string',
        ];
    }
}
