<?php

namespace App\Jobs\News;

use App\Repositories\Users\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Вызывается после создания новой записи пользователем.
 *
 * Добавляет +1 к количеству созданных пользователем записей.
 */
class ArticleAfterCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $userID
     */
    private $userID;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create a new job instance.
     */
    public function __construct($userID)
    {
        $this->userID = $userID;

        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->userRepository->getUserByID($this->userID);

        $user->news_num += 1;

        $user->save();
    }
}
