<?php

namespace App\Events\News;

use App\Models\News\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Событие "пользователь дизлайкнул запись".
 * Формирует объект с некой записи и ID пользователя.
 */
class ArticleDislikedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Article $article
     */
    public $article;
    /**
     * @var $user_id
     */
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @param Article $article
     * @param $userID
     */
    public function __construct(Article $article, $userID)
    {
        $this->article = $article;
        $this->user_id = intval($userID);
    }
}
