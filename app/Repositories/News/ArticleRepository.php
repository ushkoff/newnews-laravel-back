<?php

namespace App\Repositories\News;

use App\Models\News\Article as Model;
use App\Repositories\CoreRepository;

/**
 * Class ArticleRepository
 *
 * Данный класс работает со всеми сущностями класса Article.
 * (По умолчанию выборка всех записей/списков определяется в контексте Global News,
 * то есть нет дополнительных огранечений в выборке в виде локации и т.д.)
 *
 * @package App\Repositories\News
 */
class ArticleRepository extends CoreRepository
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * ArticleRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->categoryRepository = app(CategoryRepository::class);
    }

    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get article by ID.
     *
     * @param $id
     */
    public function getArticleByID($id)
    {
        $columns = [
            'id',
            'category_id',
            'user_id',
            'title',
            'content_html',
            'country',
            'country_code',
            'refs',
            'author_pubkey',
            'signature',
            'rating',
            'is_confirmed',
            'is_edited',
            'created_at',
            'updated_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->with(['category:id,title,slug']) // lazy load
            ->with(['user:id,username']) // lazy load
            ->findOrFail($id);

        return $result;
    }

    /**
     * Get list of all articles.
     *
     * @param $blocklist (of users)
     * @param int $quantity
     * @return array
     */
    public function getAllArticles($quantity, $blocklist = [])
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
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get search results (list of articles).
     *
     * @param $blocklist (of users)
     * @param int $quantity
     * @param $query
     */
    public function getSearchArticles($query, $quantity, $blocklist = [])
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
            ->where('title', 'LIKE', '%' . $query . '%') // searching articles
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get list of confirmed articles.
     *
     * @param $blocklist (of users)
     * @param int $quantity
     */
    public function getConfirmedArticles($quantity, $blocklist = [])
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
            ->where('is_confirmed', 1)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get list of articles by category ID.
     *
     * @param $categoryID
     * @param $blocklist (of users)
     * @param int $quantity
     */
    public function getArticlesByCategoryID($categoryID, $quantity, $blocklist = [])
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
            ->where('category_id', $categoryID) // fetching by category ID
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get articles where current user is an author.
     *
     * @param $userID
     * @param int $quantity
     */
    public function getYourArticles($userID, $quantity)
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
            ->where('user_id', $userID)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get confirmed articles where user is an author.
     *
     * @param $userID
     * @param int $quantity
     */
    public function getYourConfirmedArticles($userID, $quantity)
    {
        $columns = [
            'id',
            'user_id',
            'category_id',
            'title',
            'country',
            'rating',
            'is_confirmed',
            'cost',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->where('user_id', $userID)
            ->where('is_confirmed', 1)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     *
     * FOR HOME PAGE, SIDEBARS ETC.
     *
     */

    /**
     * Get latest articles (with content included).
     *
     * @param $quantity
     * @param array $blocklist
     */
    public function getLatestArticles($quantity, $blocklist = [])
    {
        $columns = [
            'id',
            'category_id',
            'title',
            'content_html',
            'country',
            'created_at'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blocklist)
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }

    /**
     * Get articles in random category.
     *
     * @param int $quantity
     * @param array $blacklist
     */
    public function getRandomCategoryArticles($quantity, $blacklist = [])
    {
        $columns = [
            'id',
            'user_id',
            'category_id',
            'title',
            'content_html',
            'country',
            'rating',
            'is_confirmed',
            'created_at'
        ];

        $categoryQuantity = $this->categoryRepository->getCategoryQuantity();
        $result = $this->startConditions()
            ->select($columns)
            ->whereNotIn('user_id', $blacklist)
            ->where('category_id', rand(1, $categoryQuantity))
            ->orderBy('id', 'DESC')
            ->with(['category:id,title,slug']) // lazy load
            ->paginate($quantity);

        return $result;
    }
}
