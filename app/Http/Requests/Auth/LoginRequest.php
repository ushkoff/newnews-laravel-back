<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'          => 'required|string|email|max:256',
            'password'       => 'required|string|min:10|max:100',
            'recaptchaToken' => 'string'
        ];
    }
}
