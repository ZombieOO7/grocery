<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CMS extends BaseModel
{
    use Sluggable;

    protected $table = 'cms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','page_title', 'page_slug', 'api_page_slug', 'page_content', 'meta_title', 'meta_keyword', 'meta_description', 'meta_robots', 'created_by', 'updated_by', 'status',
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
            'page_slug' => [
                'source' => 'page_title',
                'onUpdate' => true,
            ],
        ];
    }
}