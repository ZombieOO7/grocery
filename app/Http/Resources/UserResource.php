<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name??'',
            'email' => $this->email??'',
            'phone' => $this->phone??'',
            'phone_code' => $this->phone_code??'',
            'image' => $this->attachment->image_path??'',
            'status' => $this->status,
        ];
        return $user;
    }
}
