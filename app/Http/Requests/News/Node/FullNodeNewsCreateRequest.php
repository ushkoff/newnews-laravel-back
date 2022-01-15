<?php

namespace App\Http\Requests\News\Node;

use Illuminate\Foundation\Http\FormRequest;

class FullNodeNewsCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'hash' => 'required|string|min:10|max:1000',
            'content_json' => 'required|string|min:10|max:10000'
        ];
    }
}
