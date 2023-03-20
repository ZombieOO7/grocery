<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserDeviceToken extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_type', 'device_token', 'device_id', 'fcm_token', 'status','otp',
    ];
}
?>