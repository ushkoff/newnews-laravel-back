<?php

namespace App\Listeners\News;

use App\Events\News\ArticleLikedEvent;
use App\Models\News\NewsRating\ArticleDislike;
use App\Models\News\NewsRating\ArticleLike;
use App\Repositories\News\NewsRating\ArticleDislikeRepository;
use App\Repositories\News\NewsRating\ArticleLikeRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Слушатель события "пользователь лайкнул новость".
 * Обсчитывает рейтинг, обрабатывает логику добавления лайка
 * (учитывая разные моменты).
 */
class LikeArticleListener
{
    /**
     * @var ArticleLikeRepository
     */
    private $articleLikeRepository;

    /**
     * @var ArticleDislikeRepository
     */
    private $articleDislikeRepository;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->articleLikeRepository = app(ArticleLikeRepository::class);
        $this->articleDislikeRepository = app(ArticleDislikeRepository::class);
    }

    /**
     * Handle the event.
     *
     * Логика добавления лайка (с учетом различных моментов).
     *
     * @param  ArticleLikedEvent  $event
     */
    public function handle(ArticleLikedEvent $event)
    {
        $article = $event->article;

        $articleID = $article->id;
        $userID = $event->user_id;

        $isArticleAlreadyLiked = $this->articleLikeRepository
            ->getLikeIDByArticleIDAndUserID($articleID, $userID);
        $isArticleAlreadyDisliked = $this->articleDislikeRepository
            ->getDislikeIDByPostIDAndUserID($articleID, $userID);

        /**
         * Если данный пользователь ещё никак не оценивал эту запись,
         * то просто добавляем +1 лайк.
         */
        if (is_null($isArticleAlreadyLiked) && is_null($isArticleAlreadyDisliked)) {

            $article->rating += 1;
            $article->save();

            $this->createArticleLike($articleID, $userID);

        /**
         * Если данный пользователь лайкнул эту запись,
         * но там уже стоит его дизлайк, то делаем -1 дизлайк, +1 лайк => rating + 2
         */
        } elseif (is_null($isArticleAlreadyLiked) && $isArticleAlreadyDisliked) {
            $article->rating += 2;
            $article->save();

            $dislikeID = $isArticleAlreadyDisliked->id; // retrieving dislike ID

            $dislike = $this->articleDislikeRepository->getDislikeByID($dislikeID);
            $dislike->delete();

            $this->createArticleLike($articleID, $userID);

        /**
         * Если данный пользователь лайкнул эту запись,
         * но там уже стоит его ЛАЙК, то убираем этот лайк.
         */
        } elseif ($isArticleAlreadyLiked && is_null($isArticleAlreadyDisliked)) {
            $article->rating -= 1;
            $article->save();

            $likeID = $isArticleAlreadyLiked->id;

            $like = $this->articleLikeRepository->getLikeByID($likeID);
            $like->delete();
        }
    }

    /**
     * Create article like instance (in 'article_likes' table).
     *
     * @param $articleID
     * @param $userID
     */
    private function createArticleLike($articleID, $userID)
    {
        $data = [
            'article_id' => $articleID,
            'user_id' => $userID
        ];

        $articleLike = ArticleLike::create($data);

        if (! $articleLike) {
            abort(500, 'Something went wrong...');
        }
    }
}
