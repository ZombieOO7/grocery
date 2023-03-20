<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
    protected $table = 'roles';
    protected $guard_name = 'web';
    protected $fillable = [
        'name', 'created_at', 'updated_at', 'guard_name'
    ];

    /**
     * Set the role's name.
     *
     * @param  string  $name
     * @return void
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower(str_replace('_', ' ', $name));
    }
}