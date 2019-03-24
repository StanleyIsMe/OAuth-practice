<?php

namespace App\Repository;


use App\Models\ModelAbstract;
use DB;

/**
 * Class Repository
 * @package App\Repositories
 */
class Repository
{
    /**
     * @var ModelAbstract
     */
    protected $model;

    /**
     * Repository constructor.
     * @param ModelAbstract $model
     */
    public function __construct(ModelAbstract $model)
    {
        $this->model = $model;
    }

    public function beginTransaction(){
        DB::beginTransaction();
    }

    public function commit(){
        DB::commit();
    }

    public function rollBack(){
        DB::rollBack();
    }
}