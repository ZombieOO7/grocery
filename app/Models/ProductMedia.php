<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAttachmentTrait;

class ProductMedia extends Model
{
    use HasAttachmentTrait;

    protected $table = 'product_media';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','image','product_id'
    ];
}
?>