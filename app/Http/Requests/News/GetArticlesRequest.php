<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class GetArticlesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * Список новостей (для определенного пользователя, если он указан).
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quantity' => 'required|integer',
            'user_id' => 'integer|exists:users,id'
        ];
    }
}
