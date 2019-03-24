<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;

/**
 * Class Member
 * @package App\Models
 */
class Member extends ModelAbstract
{
    use Authenticatable;

    protected $table = 'member';

    protected $primaryKey = 'id';

    protected $fillable = ['email', 'name', 'fb_id', 'access_token'];

    protected $guarded = ['token'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'created_at', 'updated_at', 'expired_at'
    ];
}
