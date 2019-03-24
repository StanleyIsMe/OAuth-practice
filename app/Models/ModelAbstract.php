<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * Class Model
 * @package App\Model\Mysql
 */
abstract class ModelAbstract extends Model
{
    /**
     * ModelAbstract constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        DB::enableQueryLog();
    }
}