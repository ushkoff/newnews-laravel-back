<?php

namespace App\Repositories\News;

use App\Models\News\Category as Model;
use App\Repositories\CoreRepository;

class CategoryRepository extends CoreRepository
{
    /**
     * CategoriesRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get the number of categories.
     */
    public function getCategoryQuantity()
    {
        $result = $this->startConditions()
            ->count();

        return $result;
    }
}
