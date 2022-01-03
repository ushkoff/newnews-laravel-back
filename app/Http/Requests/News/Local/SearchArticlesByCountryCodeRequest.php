<?php

namespace App\Http\Requests\News\Local;

use Illuminate\Foundation\Http\FormRequest;

class SearchArticlesByCountryCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'      => 'required|integer|exists:users,id',
            'country_code' => 'required|string|min:1|max:5',
            'quantity'     => 'required|integer|min:1|max:10000',
            'searchQuery'  => 'required|string|min:1|max:' . config('articles.max_article_title'),
        ];
    }
}
