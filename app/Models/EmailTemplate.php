<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends BaseModel
{
    use Sluggable;

    protected $table = 'email_templates';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','title','slug','status','subject','body'
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
     * -------------------------------------------------------------
     * | The attributes return title firstletter capital.          |
     * |                                                           |
     * |  @return String                                           |
     * -------------------------------------------------------------
     */
    public function getTitleTextAttribute()
    {
        return ucfirst($this->attributes['title']);
    }
    /**
     * -------------------------------------------------------------
     * | The attributes return Subject firstletter capital.        |
     * |                                                           |
     * |  @return String                                           |
     * -------------------------------------------------------------
     */
    public function getSubjectTextAttribute()
    {
        return ucfirst($this->attributes['subject']);
    }
}
