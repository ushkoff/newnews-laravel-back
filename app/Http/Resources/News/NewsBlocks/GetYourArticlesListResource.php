<?php

namespace App\Http\Resources\News\NewsBlocks;

use Illuminate\Http\Resources\Json\JsonResource;

class GetYourArticlesListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'category'     => $this->category->title,
            'categorySlug' => $this->category->slug, // category slug to make link on category page with news
            'humanDate'    => $this->created_at_for_humans,
            'country'      => $this->country,
            'rating'       => $this->rating,
            'isConfirmed'  => $this->is_confirmed,
            'cost'         => $this->cost,
            'isEditable'   => $this->is_editable,
            'title'        => $this->title
        ];
    }
}
