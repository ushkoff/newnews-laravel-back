<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class SearchArticlesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quantity'    => 'required|integer',
            'user_id'     => 'integer|exists:users,id',
            'searchQuery' => 'required|string|min:1|max:' . config('articles.max_article_title')
        ];
    }
}
