<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;

class GetArticlesResource extends JsonResource
{
    /**
     * Get articles list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'category'     => $this->category->title,
            'categorySlug' => $this->category->slug, // to make link on category page
            'humanDate'    => $this->created_at_for_humans,
            'country'      => $this->country,
            'rating'       => $this->rating,
            'isConfirmed'  => $this->is_confirmed,
            'title'        => $this->title
        ];
    }
}
