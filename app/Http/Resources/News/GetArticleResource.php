<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;

class GetArticleResource extends JsonResource
{
    /**
     * Article resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'category'     => $this->category->title, // category title
            'categoryID'   => $this->category->id,    // category id
            'categorySlug' => $this->category->slug,  // category slug to make link
            'userID'       => $this->user->id,
            'username'     => $this->user->username,
            'date'         => $this->created_at_formatted, // up to minutes (UTC)
            'isEdited'     => $this->is_edited,
            'isEditable'   => $this->is_editable,
            'country'      => $this->country,
            'countryCode'  => $this->country_code,
            'refs'         => $this->refs,
            'author_pubkey'=> $this->author_pubkey,
            'signature'    => $this->signature,
            'rating'       => $this->rating,
            'isConfirmed'  => $this->is_confirmed,
            'title'        => $this->title,
            'content'      => $this->content_html
        ];
    }
}
