<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;

/**
 * Class Constellation
 * @package App\Models
 */
class Constellation extends ModelAbstract
{
    use Authenticatable;

    protected $table = 'constellation';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
