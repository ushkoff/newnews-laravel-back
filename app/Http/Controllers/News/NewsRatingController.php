<?php

namespace App\Http\Controllers\News;

use App\Events\News\ArticleDislikedEvent;
use App\Events\News\ArticleLikedEvent;
use App\Http\Requests\Auth\UserRequest;
use App\Repositories\News\ArticleRepository;
use App\Repositories\News\NewsRating\ArticleRatingRepository;

/**
 * Контроллер управления рейтингом записи статьи:
 * добавления лайков/дизлайков та их проверки, получение рейтинга etc.
 */
class NewsRatingController extends BaseController
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var ArticleRatingRepository
     */
    private $articleRatingRepository;

    /**
     * NewsRatingController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->articleRepository = app(ArticleRepository::class);
        $this->articleRatingRepository = app(ArticleRatingRepository::class);
    }

    /**
     * Get article rating.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRating($id)
    {
        $rating = $this->articleRatingRepository->getArticleRatingByID($id);

        return response()->json($rating, 200);
    }

    /**
     * Check if article is already liked.
     *
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isArticleLikedByUser($id, UserRequest $request)
    {
        $userID = intval($request->user_id);
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        $isLiked = $this->articleRatingRepository->isArticleLikedByUserID($id, $userID);

        return response()->json(['message' => $isLiked], 200);
    }

    /**
     * Check if article is already disliked.
     *
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isArticleDislikedByUser($id, UserRequest $request)
    {
        $userID = intval($request->user_id);
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        $isDisliked = $this->articleRatingRepository->isArticleDislikedByUserID($id, $userID);

        return response()->json(['message' => $isDisliked], 200);
    }

    /**
     * [Action] Like article.
     *
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeArticle($id, UserRequest $request)
    {
        $article = $this->articleRepository->getArticleByID($id);
        $userID = $request->user_id;
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        event(new ArticleLikedEvent($article, $userID));

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * [Action] Dislike article.
     *
     * @param $id
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dislikeArticle($id, UserRequest $request)
    {
        $article = $this->articleRepository->getArticleByID($id);
        $userID = $request->user_id;
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        event(new ArticleDislikedEvent($article, $userID));

        return response()->json(['message' => 'Success'], 200);
    }
}
