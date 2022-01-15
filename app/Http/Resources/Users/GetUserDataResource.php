<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class GetUserDataResource extends JsonResource
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
            'id'                   => $this->id,
            'username'             => $this->username,
            'email'                => $this->email,
            'newsNumber'           => $this->news_num,
            'country'              => $this->country,
            'countryCode'          => $this->country_code,
            'timezone'             => $this->timezone,
            'dateRegistered'       => $this->created_at_formatted
        ];
    }
}
