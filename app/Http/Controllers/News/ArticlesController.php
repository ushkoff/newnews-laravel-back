<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\CreateArticleRequest;
use App\Http\Requests\News\DeleteArticleRequest;
use App\Http\Requests\News\GetArticlesRequest;
use App\Http\Requests\News\SearchArticlesRequest;
use App\Http\Requests\News\UpdateArticleRequest;
use App\Http\Resources\News\GetArticleResource;
use App\Http\Resources\News\GetArticlesResource;
use App\Jobs\News\ArticleAfterCreateJob;
use App\Jobs\News\ArticleAfterDeleteJob;
use App\Models\News\Article;
use App\Repositories\News\ArticleRepository;
use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class ArticlesController
 *
 * Данный класс работает со всеми сущностями класса Article.
 * (По умолчанию все действия с записями/списками определяется в контексте Global News,
 * то есть нет дополнительных огранечений в выборке в виде локации и т.д.)
 *
 * @package App\Http\Controllers\News
 */
class ArticlesController extends BaseController
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
     * GlobalNewsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->articleRepository = app(ArticleRepository::class);
        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Get list of articles.
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) $blocklist = $this->userRepository->getUserBlocklistByID($userID);

        $articlesList = $this->articleRepository->getAllArticles($quantity, $blocklist);

        return GetArticlesResource::collection($articlesList);
    }

    /**
     * Get articles list of search results.
     *
     * @param SearchArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSearch(SearchArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) $blocklist = $this->userRepository->getUserBlocklistByID($userID);

        $query = $request->searchQuery;
        $searchArticlesList = $this->articleRepository->getSearchArticles($query, $quantity, $blocklist);

        return GetArticlesResource::collection($searchArticlesList);
    }

    /**
     * Get articles list by news category.
     *
     * @param GetArticlesRequest $request
     * @param $categoryID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getByCategory(GetArticlesRequest $request, $categoryID)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) $blocklist = $this->userRepository->getUserBlocklistByID($userID);

        $categoryArticlesList= $this->articleRepository->getArticlesByCategoryID($categoryID, $quantity, $blocklist);

        return GetArticlesResource::collection($categoryArticlesList);
    }

    /**
     * Get only confirmed articles list.
     *
     * @param GetArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOnlyConfirmed(GetArticlesRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $blocklist = [];
        if (! is_null($userID)) $blocklist = $this->userRepository->getUserBlocklistByID($userID);

        $confirmedArticlesList = $this->articleRepository->getConfirmedArticles($quantity, $blocklist);

        return GetArticlesResource::collection($confirmedArticlesList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateArticleRequest $request)
    {
//        // Verify recaptcha
//        $recaptcha_token = $request->recaptchaToken;
//        $ip = $request->ip();
//        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
//        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
//            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
//        }

        $data = [
            'category_id'    => $request->category_id,
            'user_id'        => $request->user_id,
            'title'          => $request->title,
            'content_html'   => $request->content_html,
            'country'        => $request->country,
            'country_code'   => $request->country_code,
            'refs'           => $request->refs,
            'author_pubkey'  => $request->author_pubkey,
            'signature'      => $request->signature
        ];

        $article = Article::create($data);

        if ($article) {
            $userID = $article->user_id;

            $job = new ArticleAfterCreateJob($userID);
            $this->dispatch($job);

            return response()->json(['message' => 'Your article has been successfully added!'], 200);
        } else {
            return response()->json(['message' => 'Something went wrong...'], 500);
        }
    }

    /**
     * Get specific article data (by ID).
     *
     * @param $id
     * @return GetArticleResource
     */
    public function show($id)
    {
        $article = $this->articleRepository->getArticleByID($id);

        return new GetArticleResource($article);
    }

    /**
     * Update specific article (by ID).
     *
     * @param  UpdateArticleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateArticleRequest $request, $id)
    {
//        // Verify recaptcha
//        $recaptcha_token = $request->recaptchaToken;
//        $ip = $request->ip();
//        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
//        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
//            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
//        }

        $data = [
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'content_html'  => $request->content_html,
            'refs'          => $request->refs
        ];

        $article = $this->articleRepository->getArticleByID($id);

        // if about 4 hours have passed ...
        if (! $article->is_editable) {
            abort(403);
        }

        $result = $article->update($data);

        $article->is_edited = 1;
        $article->save();

        if ($result) {
            return response()->json(['message' => 'Your article has been successfully edited!'], 200);
        } else {
            return response()->json(['message' => 'Something went wrong...'], 500);
        }
    }

    /**
     * Remove specific article (by ID).
     *
     * @param $id
     * @param DeleteArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, DeleteArticleRequest $request)
    {
//        // Verify recaptcha
//        $recaptcha_token = $request->recaptchaToken;
//        $ip = $request->ip();
//        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
//        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
//            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
//        }

        $article = Article::find($id);
        $userID = intval($request->user_id);

        if ($article->user_id === $userID) {
            $article->forceDelete();

            if ($article->is_confirmed) {
                Log::channel('actions')->info('Confirmed article "'.$article->title.'" under ID '.$article->id.' was deleted.');
            }

            // Delete likes and dislikes related to this article
            $job = new ArticleAfterDeleteJob($id);
            $this->dispatch($job);

            return response()->json(['message' => 'Article has been successfully deleted.'], 200);
        } else {
            return response()->json(['message' => 'Something went wrong...'], 403);
        }
    }
}
