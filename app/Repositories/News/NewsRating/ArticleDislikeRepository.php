<?php

namespace App\Repositories\News\NewsRating;

use App\Models\News\NewsRating\ArticleDislike as Model;
use App\Repositories\CoreRepository;

/**
 * Class ArticleDislikeRepository
 *
 * Управление сущносятми типа ArticleDislike.
 * Представлена возможность получения количества дизлайков для конкретной записи,
 * определения существует ли дизлайк этого пользователя на эту запись etc
 *
 * @package App\Repositories\News
 */
class ArticleDislikeRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get dislike IDs of article (by article ID).
     *
     * @param $article_id
     * @return array
     */
    public function getDislikeIDsByArticleID($article_id)
    {
        $dislikes = $this->startConditions()
            ->where('article_id', $article_id)
            ->get();

        $dislikeIDs = [];
        foreach ($dislikes as $dislike) {
            array_push($dislikeIDs, $dislike->id);
        }

        $dislikeIDs = array_values($dislikeIDs);

        return $dislikeIDs;
    }

    /**
     * Get dislike instance by ArticleDislike ID.
     *
     * @param $dislike_id
     */
    public function getDislikeByID($dislike_id)
    {
        return $this->startConditions()
            ->findOrFail($dislike_id);
    }

    /**
     * Get dislike instance by articleID and userID
     * (used to check if user has already disliked specific article).
     *
     * @param $article_id
     * @param $user_id
     * @return array
     */
    public function getDislikeIDByPostIDAndUserID($article_id, $user_id)
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
