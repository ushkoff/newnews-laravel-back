<?php

namespace App\Repositories\News\NewsRating;

use App\Models\News\NewsRating\ArticleLike as Model;
use App\Repositories\CoreRepository;

/**
 * Class ArticleLikeRepository
 *
 * Управление сущносятми типа ArticleLike.
 * Представлена возможность получения количества лайков для конкретной записи,
 * определения существует ли лайк этого пользователя на эту запись etc.
 *
 * @package App\Repositories\News
 */
class ArticleLikeRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get like IDs of article (by article ID).
     *
     * @param $article_id
     * @return array
     */
    public function getLikeIDsByArticleID($article_id)
    {
        $likes = $this->startConditions()
            ->where('article_id', $article_id)
            ->get();

        $likeIDs = [];
        foreach ($likes as $like) {
            array_push($likeIDs, $like->id);
        }

        $likeIDs = array_values($likeIDs);

        return $likeIDs;
    }

    /**
     * Get like instance by ArticleLike ID.
     *
     * @param $like_id
     */
    public function getLikeByID($like_id)
    {
        return $this->startConditions()
            ->findOrFail($like_id);
    }

    /**
     * Get like instance by articleID and userID
     * (used to check if user has already liked specific article).
     *
     * @param $article_id
     * @param $user_id
     * @return array
     */
    public function getLikeIDByArticleIDAndUserID($article_id, $user_id)
    {
        $columns = [
            'id'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->where('article_id', $article_id)
            ->where('user_id', $user_id)
            ->first();

        return $result;
    }
}
