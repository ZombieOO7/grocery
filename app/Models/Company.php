<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

class Company extends BaseModel
{
    use Sluggable;
    protected $table ='companies';
    protected $fillable =['title', 'uuid', 'slug','status'];
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
}
