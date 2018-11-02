<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class WeappAuthorizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string',
        ];
    }
}