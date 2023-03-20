<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = [];
        foreach($this->productMedia as $image){
            array_push($images,$image->attachment->image_path);
        }
        return [
            'id' => $this->id,
            'name' => $this->title,
            'price' => $this->price,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'category' => $this->category->title??'',
            'stock_status' => $this->stock_status,
            'images' => $images
        ];
    }
}
