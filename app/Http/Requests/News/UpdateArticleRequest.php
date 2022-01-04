<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'        => 'required|integer|exists:users,id',
            'category_id'    => 'required|integer|exists:categories,id',
            'title'          => 'required|string|min:10|max:' . config('articles.max_article_title'),
            'content_html'   => 'required|string|min:50|max:' . config('articles.max_article_content'),
            'refs'           => 'max:' . config('articles.max_article_refs'),
            'recaptchaToken' => 'string'
        ];
    }
}
