<?php

namespace App\Http\Controllers\News;

use App\Http\Requests\News\Local\GetArticlesByCountryCodeRequest;
use App\Http\Requests\News\Local\SearchArticlesByCountryCodeRequest;
use App\Http\Resources\News\GetArticlesResource;
use App\Repositories\News\LocalNewsRepository;
use App\Repositories\Users\UserRepository;

/**
 * Class LocalNewsController
 *
 * Данный класс работает с "локальными" новосятями-сущностями класса Article.
 * (По умолчанию все действия с записями/списками определяется в контексте Local News,
 * то есть в рамках определенной локации (страны) etc)
 *
 * @package App\Http\Controllers\News
 */
class LocalNewsController extends BaseController
{
    /**
     * @var LocalNewsRepository
     */
    private $localNewsRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * LocalNewsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->localNewsRepository = app(LocalNewsRepository::class);
        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Get list of local news articles by user's country.
     *
     * @param GetArticlesByCountryCodeRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(GetArticlesByCountryCodeRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $country_code = $request->country_code;

        $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        $localNewsList = $this->localNewsRepository->getAllArticlesByCountryCode($country_code, $quantity, $blocklist);

        return GetArticlesResource::collection($localNewsList);
    }

    /**
     * Get local news article list of search results.
     *
     * @param SearchArticlesByCountryCodeRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSearch(SearchArticlesByCountryCodeRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $country_code = $request->country_code;
        $query = $request->searchQuery;

        $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        $searchLocalNewsList = $this->localNewsRepository->getSearchArticlesByCountryCode($query, $country_code, $quantity, $blocklist);

        return GetArticlesResource::collection($searchLocalNewsList);
    }

    /**
     * Get local news article list by category ID.
     *
     * @param GetArticlesByCountryCodeRequest $request
     * @param $categoryID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getByCategory(GetArticlesByCountryCodeRequest $request, $categoryID)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $country_code = $request->country_code;

        $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        $categoryLocalNewsList = $this->localNewsRepository
            ->getArticlesByCountryCodeAndCategoryID($categoryID, $country_code, $quantity, $blocklist);

        return GetArticlesResource::collection($categoryLocalNewsList);
    }

    /**
     * Get article list of only confirmed local news.
     *
     * @param GetArticlesByCountryCodeRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function onlyConfirmed(GetArticlesByCountryCodeRequest $request)
    {
        $quantity = $request->quantity;
        $userID = $request->user_id;
        $country_code = $request->country_code;

        $blocklist = $this->userRepository->getUserBlocklistByID($userID);
        $confirmedLocalNewsList = $this->localNewsRepository
            ->getConfirmedArticlesByCountryCode($country_code, $blocklist);

        return GetArticlesResource::collection($confirmedLocalNewsList);
    }
}
