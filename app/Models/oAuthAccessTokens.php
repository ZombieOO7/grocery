<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class oAuthAccessTokens extends Model
{
    protected $primaryKey = 'id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'name', 'scopes', 'revoked'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

	/**
     * Dates to be treated as Carbon instances
     *
     * @var array
     */
    public $dates = [

    ];
}