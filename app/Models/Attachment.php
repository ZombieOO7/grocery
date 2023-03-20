<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Attachment extends Model
{
    protected $fillable =['url','attachmentable_id','attachmentable_type'];

    protected $appends = ['image_path'];

    public function getImagePathAttribute(): string
    {
        if($this->url != null && file_exists(storage_path('app/public/'.$this->url))){
            return url('storage/app/public/'.$this->url);
        }else{
            return $this->_defaultImagePath();
        }
    }

    private function _defaultImagePath(): string
    {
            return asset('images/no-image-available.jpg');
    }

    /**
     * Get the owning attachmentable model.
     */
    public function attachmentable()
    {
        return $this->morphTo();
    }

    public function getImagePath2Attribute()
    {
        if($this->url != null && file_exists(storage_path('app/public/'.$this->url))){
            return url('storage/app/public/'.$this->url);
        }
        return null;
    }
}
