<?php

namespace App\Models;

use App\Traits\HasAttachmentTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends BaseModel
{
    use Sluggable;
    use HasAttachmentTrait;

    protected $table = 'sub_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','title','slug','status','image','category_id'
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
    public function category(){
        return $this->belongsTo('App\Models\Category','category_id');
    }
}
