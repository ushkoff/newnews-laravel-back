<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'       => 'required|string|min:2|max:25|unique:users',
            'email'          => 'required|string|email|max:256|unique:users',
            'password'       => 'required|string|min:10|max:100',
            'country'        => 'required|string|min:1|max:100',
            'countryCode'    => 'required|string|min:1|max:5',
            'timezone'       => 'required|string|min:1|max:100',
            'recaptchaToken' => 'string'
        ];
    }
}
