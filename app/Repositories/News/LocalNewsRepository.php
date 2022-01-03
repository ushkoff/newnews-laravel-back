<?php

namespace App\Repositories\News;

use App\Models\News\Article as Model;
use App\Repositories\CoreRepository;

/**
 * Class LocalNewsRepository
 *
 * Данный класс работает с "локальными" новосятями-сущностями класса Article.
 * (По умолчанию все действия с записями/списками определяется в контексте Local News,
 * то есть в рамках определенной локации (страны) etc)
 *
 * @package App\Repositories\News
 */
class LocalNewsRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get article list of all Local News.
     *
     * @param $country_code
     * @param int $quantity
     * @param $blocklist
     * @return array
     */
    public function getAllArticlesByCountryCode($country_code, $quantity, $blocklist = [])
    {
        $columns = [
            'id',
            'title',
            'user_id',
            'category_id',
            'country',
            'rating',
            'is_confirmed',
            'country_code',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blocklist)
            ->where('country_code', $country_code)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get local news article list of search results.
     *
     * @param $query
     * @param $country_code
     * @param $quantity
     * @param $blocklist
     * @return array
     */
    public function getSearchArticlesByCountryCode($query, $country_code, $quantity, $blocklist = [])
    {
        $columns = [
            'id',
            'title',
            'user_id',
            'category_id',
            'country',
            'rating',
            'is_confirmed',
            'country_code',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blocklist)
            ->where('country_code', $country_code)
            ->where('title', 'LIKE', '%' . $query . '%') // searching articles
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get list of confirmed local news articles.
     *
     * @param $country_code
     * @param $quantity
     * @param $blocklist
     */
    public function getConfirmedArticlesByCountryCode($country_code, $quantity, $blocklist = [])
    {
        $columns = [
            'id',
            'title',
            'user_id',
            'category_id',
            'country',
            'rating',
            'is_confirmed',
            'country_code',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blocklist)
            ->where('country_code', $country_code)
            ->where('is_confirmed', 1)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get local news article list by category ID.
     *
     * @param $categoryID
     * @param $country_code
     * @param $quantity
     * @param $blocklist
     */
    public function getArticlesByCountryCodeAndCategoryID($categoryID, $country_code, $quantity, $blocklist = [])
    {
        $columns = [
            'id',
            'user_id',
            'category_id',
            'title',
            'country',
            'rating',
            'is_confirmed',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blocklist)
            ->where('country_code', $country_code)
            ->where('category_id', $categoryID) // searching articles
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }
}
