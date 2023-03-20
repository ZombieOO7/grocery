<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

class PermissionMaster extends BaseModel
{
    use Sluggable;

    protected $table = 'permission_masters';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','title','slug','status','user_type','permission_status'
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


    /**
     * --------------------------------------------------------------
     * | Scope a query to active or inactive permission.            |
     * |                                                            |
     * | @param  $query, $status                                    |
     * | @return array                                              |
     * --------------------------------------------------------------
     */
    public function scopeStatusSearch($query, $status)
    {
        return $query->where('permission_status', ($status == 'On') ? 1 : 0);
    }
}
