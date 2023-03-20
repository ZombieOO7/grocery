<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    protected $guard_name = 'admin';

    protected $table = 'admins';

    protected $fillable = ['first_name', 'last_name', 'email', 'email_verified_at', 'password', 'status'];

    protected $hidden = ['password', 'remember_token'];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Set Active/Inactive tag in List
     *
     * @return void
     */
    public function getActiveTagAttribute()
    {
        if($this->status == '0'){
            return '<span class="m-badge  m-badge--danger m-badge--wide">'.__('formname.inactive').'</span>';
        }
        else{
            return '<span class="m-badge  m-badge--success m-badge--wide">'.__('formname.active').'</span>';
        }
    }

    /**
     * Search Active/Inactive records in List
     *
     * @return void
     */
    public function scopeActiveSearch($query, $status)
    {
        return $query->where('status', ($status == 'Active') ? 1 : 0);
    }

    /**
     * This attribute display user fullname.
     *
     * @param string attribute
     */

    public function getFullNameTextAttribute($value)
    {
        return ucfirst($this->first_name . ' ' . $this->last_name);
    }

    //Send password reset notification
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}