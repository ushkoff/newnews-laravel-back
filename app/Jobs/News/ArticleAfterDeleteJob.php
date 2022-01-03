<?php

namespace App\Jobs\News;

use App\Models\News\NewsRating\ArticleDislike;
use App\Models\News\NewsRating\ArticleLike;
use App\Repositories\News\NewsRating\ArticleDislikeRepository;
use App\Repositories\News\NewsRating\ArticleLikeRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Вызывается после удаления новой записи пользователем.
 *
 * Удаляет все записи про лайки/дизлайки этой новости с таблиц соотвественно.
 */
class ArticleAfterDeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $article_id
     */
    private $article_id;

    /**
     * @var ArticleLikeRepository
     */
    private $articleLikeRepository;

    /**
     * @var ArticleDislikeRepository
     */
    private $articleDislikeRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($article_id)
    {
        $this->article_id = $article_id;

        $this->articleLikeRepository = app(ArticleLikeRepository::class);
        $this->articleDislikeRepository = app(ArticleDislikeRepository::class);
    }

    /**
     * Execute the job.
     *
     * Удаление лайков и дизлайков к новости, что была удалена.
     *
     * @return void
     */
    public function handle()
    {
        // Get marks (likes and dislikes) IDs of this article
        $likeRecordsIDs = $this->articleLikeRepository->getLikeIDsByArticleID($this->article_id);
        $dislikeRecordsIDs = $this->articleDislikeRepository->getDislikeIDsByArticleID($this->article_id);

        // Delete marks
        ArticleLike::find($likeRecordsIDs)->each(function ($like, $key) {
            $like->forceDelete();
        });
        ArticleDislike::find($dislikeRecordsIDs)->each(function ($dislike, $key) {
            $dislike->forceDelete();
        });
    }
}
