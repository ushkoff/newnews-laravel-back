<?php

namespace App\Repositories\News\NewsRating;

use App\Models\News\Article as Model;
use App\Repositories\CoreRepository;

/**
 * Class ArticleRatingRepository
 *
 * Вспомогательный репозиторий управления сущностями Article.
 * Используется для определения факта лайка/дизлайка конкретного пользователя этой новости,
 * получения рейтинга новости etc.
 *
 * @package App\Repositories\News
 */
class ArticleRatingRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @var ArticleLikeRepository
     */
    private $articleLikeRepository;

    /**
     * @var ArticleDislikeRepository
     */
    private $articleDislikeRepository;

    /**
     * ArticleRatingRepository constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->articleLikeRepository = app(ArticleLikeRepository::class);
        $this->articleDislikeRepository = app(ArticleDislikeRepository::class);
    }

    /**
     * Get article rating.
     *
     * @param $article_id
     * @return integer
     */
    public function getArticleRatingByID($article_id)
    {
        $columns = [
            'rating'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->find($article_id);

        return $result;
    }

    /**
     * Check if article is already confirmed.
     *
     * @param  int $article_id
     */
    public function isArticleConfirmed($article_id)
    {
        return $this->startConditions()
            ->where('is_confirmed', 1)
            ->find($article_id);
    }

    /**
     * Check if article is already liked.
     *
     * @param $article_id
     * @param $user_id
     * @return bool
     */
    public function isArticleLikedByUserID($article_id, $user_id)
    {
        $isArticleLiked = $this->articleLikeRepository
            ->getLikeIDByArticleIDAndUserID($article_id, $user_id);

        if (! is_null($isArticleLiked)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if article is already disliked.
     *
     * @param $article_id
     * @param $user_id
     * @return bool
     */
    public function isArticleDislikedByUserID($article_id, $user_id)
    {
        $isArticleDisliked = $this->articleDislikeRepository
            ->getDislikeIDByPostIDAndUserID($article_id, $user_id);

        if (! is_null($isArticleDisliked)) {
            return true;
        } else {
            return false;
        }
    }
}
