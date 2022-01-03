<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id'    => 'required|integer|exists:categories,id',
            'user_id'        => 'required|integer|exists:users,id',
            'title'          => 'required|string|min:10|max:' . config('articles.max_article_title'),
            'content_html'   => 'required|string|min:50|max:' . config('articles.max_article_content'),
            'country'        => 'required|string|min:1|max:100',
            'country_code'   => 'required|string|min:1|max:5',
            'author_pubkey'  => 'string|min:10|max:1000', // for some time unrequired...
            'signature'      => 'string|min:10|max:1000', // the same.
            'refs'           => 'string|max:' . config('articles.max_article_refs'),
            'recaptchaToken' => 'string'
        ];
    }
}
