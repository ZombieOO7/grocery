<?php

namespace App\Models;

use App\Traits\HasAttachmentTrait;
use Cviebrock\EloquentSluggable\Sluggable;

class Product extends BaseModel
{
    use Sluggable;
    use HasAttachmentTrait;
    protected $table = 'products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','title','slug','status','sub_category_id','category_id', 'stock_status','short_description','description','price'
    ];

        /**
     * -------------------------------------------------------------
     * | Return the sluggable configuration array for this model.  |
     * |                                                           |
     * | @return array                                             |
     * -------------------------------------------------------------
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
            ],
        ];
    }

    public function productMedia(){
        return $this->hasMany('App\Models\ProductMedia','product_id')->with('attachment');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category','category_id');
    }
    public function subCategory(){
        return $this->belongsTo('App\Models\SubCategory','sub_category_id');
    }
}
?>