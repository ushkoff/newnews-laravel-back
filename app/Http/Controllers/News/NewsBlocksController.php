<?php

namespace App\Http\Controllers\News;

use App\Http\Requests\News\GetArticlesRequest;
use App\Http\Resources\News\NewsBlocks\GetContentArticlesListResource;
use App\Http\Resources\News\NewsBlocks\GetYourArticlesListResource;
use App\Repositories\News\ArticleRepository;
use App\Repositories\Users\UserRepository;

/**
 * Class NewsBlocksController
 *
 * Данный класс работает с выборками новосятей-сущностей класса Article.
 * Представлены разношерстные выборки записей, как и для личного кабинета пользователя,
 * так и для сайдбаров, домайшней странице etc.
 *
 * @package App\Http\Controllers\News
 */
class NewsBlocksController extends BaseController
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * NewsBlocksController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->articleRepository = app(ArticleRepository::class);
        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Get list of articles where user is author.
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getYourArticles(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = intval($request->user_id);
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        $yourArticlesList = $this->articleRepository->getYourArticles($userID, $quantity);

        return GetYourArticlesListResource::collection($yourArticlesList);
    }

    /**
     * Get confirmed list of articles where user is author.
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getYourConfirmedArticles(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = intval($request->user_id);
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        $yourConfirmedArticlesList = $this->articleRepository->getYourConfirmedArticles($userID, $quantity);

        return GetYourArticlesListResource::collection($yourConfirmedArticlesList);
    }

    /**
     * Get latest list of articles (with content included)
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getLatestArticles(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) {
            if ($userID != auth()->guard('api')->user()->id) {
                abort(401);
            }
            $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        }

        $latestArticlesList = $this->articleRepository->getLatestArticles($quantity, $blocklist);

        return GetContentArticlesListResource::collection($latestArticlesList);
    }

    /**
     * Get global news, that may be interesting for user.
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getRandomCategoryArticles(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) {
            if ($userID != auth()->guard('api')->user()->id) {
                abort(401);
            }
            $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        }

        $randomCategoryArticlesList = $this->articleRepository->getRandomCategoryArticles($quantity, $blocklist);

        return GetContentArticlesListResource::collection($randomCategoryArticlesList);
    }
}
