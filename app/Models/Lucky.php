<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;

/**
 * Class Lucky
 * @package App\Models
 */
class Lucky extends ModelAbstract
{
    use Authenticatable;

    protected $table = 'lucky';

    protected $primaryKey = 'id';

    protected $fillable = [
        'constellation_id',
        'average_fortune',
        'average_description',
        'love_fortune',
        'love_description',
        'career_fortune',
        'career_description',
        'wealth_fortune',
        'wealth_description',
        'at_day',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
