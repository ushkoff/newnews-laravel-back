<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CoreRepository
 *
 * @package App\Repositories
 *
 * Repository of work with the entity.
 * Can produce datasets.
 * Cannot create/modify entities.
 */
abstract class CoreRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * CoreRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    /**
     * @return mixed
     */
    abstract protected function getModelClass();

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Model|mixed
     */
    protected function startConditions()
    {
        return clone $this->model;
    }
}