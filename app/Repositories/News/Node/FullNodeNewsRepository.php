<?php

namespace App\Repositories\News\Node;

use App\Models\News\Node\FullNodeArticle as Model;
use App\Repositories\CoreRepository;

/**
 * Class ArticleRepository
 *
 * Данный класс работает со всеми сущностями класса FullNodeArticle.
 * Представлена функция выборки "полных" (full) новостей из таблицы
 *
 * @package App\Repositories\News
 */
class FullNodeNewsRepository extends CoreRepository
{
    /**
     * FullNodeNewsRepository constructor.
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
     * Get full news by it's hash.
     *
     * @param string $hash
     * @return array
     */
    public function getByHash($hash)
    {
        $columns = [
            'id',
            'content_json'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->where('hash', $hash)
            ->first();

        return $result;
    }
}
