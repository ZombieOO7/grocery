<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    protected $table = 'web_settings';
    public $timestamps = true;
    protected $fillable = [
        'logo','google_url','facebook_url', 'favicon','twitter_url','youtube_url','meta_keywords',
        'meta_description','android_app_icon','ios_app_icon','ios_fcm_key','android_fcm_key','send_email','notify'
    ];
}
