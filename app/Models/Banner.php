<?php

namespace App\Models;

use App\Traits\HasAttachmentTrait;
use Illuminate\Database\Eloquent\Model;

class Banner extends BaseModel
{
    use HasAttachmentTrait;

    protected $table = 'banners';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','status','image'
    ];
}
?>