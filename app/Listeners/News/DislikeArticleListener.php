<?php

namespace App\Listeners\News;

use App\Events\News\ArticleDislikedEvent;
use App\Models\News\NewsRating\ArticleDislike;
use App\Repositories\News\NewsRating\ArticleDislikeRepository;
use App\Repositories\News\NewsRating\ArticleLikeRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Слушатель события "пользователь дизлайкнул новость".
 * Обсчитывает рейтинг, обрабатывает логику добавления дизлайка
 * (учитывая разные моменты).
 */
class DislikeArticleListener
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
     * Логика добавления дизлайка (с учетом различных моментов).
     *
     * @param  ArticleDislikedEvent  $event
     */
    public function handle(ArticleDislikedEvent $event)
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
         * то просто добавляем +1 дизлайк => -1 рейтинг.
         */
        if (is_null($isArticleAlreadyLiked) && is_null($isArticleAlreadyDisliked)) {
            $article->rating -= 1;
            $article->save();

            $this->createArticleDislike($articleID, $userID);

        /**
         * Если данный пользователь дизлайкнул эту запись,
         * но там уже стоит его ЛАЙК, то делаем +1 дизлайк, -1 лайк => rating -2
         */
        } elseif (is_null($isArticleAlreadyDisliked) && $isArticleAlreadyLiked) {
            $article->rating -= 2;
            $article->save();

            $likeID = $isArticleAlreadyLiked->id; // retrieving like ID

            $like = $this->articleLikeRepository->getLikeByID($likeID);
            $like->delete();

            $this->createArticleDislike($articleID, $userID);

        /**
         * Если данный пользователь дизлайкнул эту запись,
         * но там уже стоит его ДИЗЛАЙК, то убираем этот дизлайк => рейтинг + 1
         */
        } elseif ($isArticleAlreadyDisliked && is_null($isArticleAlreadyLiked)) {
            $article->rating += 1;
            $article->save();

            $dislikeID = $isArticleAlreadyDisliked->id;

            $dislike = $this->articleDislikeRepository->getDislikeByID($dislikeID);
            $dislike->delete();
        }
    }

    /**
     * Create article dislike instance (in 'article_dislikes' table).
     *
     * @param $articleID
     * @param $userID
     */
    private function createArticleDislike($articleID, $userID)
    {
        $data = [
            'article_id' => $articleID,
            'user_id' => $userID
        ];

        $articleDislike = ArticleDislike::create($data);

        if (! $articleDislike) {
            abort(500, 'Something went wrong...');
        }
    }
}
