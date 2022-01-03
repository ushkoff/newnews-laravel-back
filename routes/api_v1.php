<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Version 1 Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Subject / Русский
 * Route_type route/to/method {param1,param2,...}: returns data:{...}
 */
Route::get('/test', function (Request $request) {
    return 1;
});

/**
 * News
 *
 * Маршруты к методам контроллеров пространства имен News.
 * Данные маршруты имеют префикс /news/
 */
Route::group(['namespace' => 'News', 'prefix' => 'news'], function () {

    /**
     * Global News / Все новости
     *
     * Группа маршрутов к общему управлению новостями.
     * Данные маршруты имеют префикс /global-news/
     */
    Route::group(['prefix' => 'global-news'], function () {
        /**
         * Get all articles / Вывести список всех записей.
         * POST news/global-news/ {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('/', 'ArticlesController@index')
            ->name('news.globalNews.index');
        /**
         * Get search articles list / Вывести список записей за поиском по строке.
         * POST news/global-news/search {searchQuery, quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('search', 'ArticlesController@getSearch')
            ->name('news.globalNews.searchResults');
        /**
         * Get articles list by category ID / Вывести список записей за ID категории новостей.
         * POST news/global-news/category/{id} {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('category/{id}', 'ArticlesController@getByCategory')
            ->name('news.globalNews.categoryResults');
        /**
         * Get only confirmed articles list / Вывести список подтвержденных сообществом записей.
         * POST news/global-news/confirmed {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('confirmed', 'ArticlesController@getOnlyConfirmed')
            ->name('news.globalNews.confirmed');

        /**
         * Get article data by ID / Вывести данные записи за её ID.
         * GET news/global-news/{id}: JSON {id,category,categoryID,categorySlug,userID,username,date,isEdited,isEditable,country,countryCode,refs,author_pubkey,signature,rating,isConfirmed,title,content}
         */
        Route::get('{id}', 'ArticlesController@show')
            ->name('news.globalNews.show');

        /**
         * Create article / Создание записи.
         * POST news/global-news/store {category_id,user_id,title,content_html,country,country_code,author_pubkey,signature,refs,recaptchaToken}: msg
         */
        Route::post('store', 'ArticlesController@store')
            ->name('news.globalNews.store');

        /**
         * Update article / Редактирование записи.
         * POST news/global-news/{id}/update {category_id,title,content_html,refs,recaptchaToken}: msg
         */
        Route::post('{id}/update', 'ArticlesController@update')
            ->name('news.globalNews.update');

        /**
         * Delete article / Удалить запись.
         * POST news/global-news/{id}/delete {user_id}: msg
         */
        Route::post('{id}/delete', 'ArticlesController@destroy')
            ->name('news.globalNews.destroy');

    }); // prefix => 'global-news'

    /**
     * Local News / Локальные новости (>>> в пределах страны)
     *
     * Группа маршрутов к управлению локальными новостями.
     * Данные маршруты имеют префикс /local-news/
     */
    Route::group(['prefix' => 'local-news'], function () {
        /**
         * Get all local news articles by country code / Вывести список всех локальных новостей за кодом страны.
         * POST news/local-news/ {quantity, user_id, country_code}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('/', 'LocalNewsController@index')
            ->name('news.localNews.index');
        /**
         * Get search local news article list / Вывести список записей локальных новостей за поиском по строке.
         * POST news/local-news/search {searchQuery, quantity, user_id, country_code}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('search', 'LocalNewsController@getSearch')
            ->name('news.localNews.searchResults');
        /**
         * Get local news article list by category ID / Вывести список записей локальных новостей за ID категории.
         * POST news/local-news/category/{id} {quantity, user_id, country_code}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('category/{id}', 'LocalNewsController@getByCategory')
            ->name('news.localNews.categoryResults');
        /**
         * Get only confirmed local news article list / Вывести список подтвержденных сообществом записей локальных новостей.
         * POST news/local-news/confirmed {quantity, user_id, country_code}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,title}, {...}]
         */
        Route::post('confirmed', 'LocalNewsController@onlyConfirmed')
            ->name('news.localNews.confirmed');
    }); // prefix => 'local-news'

    /**
     * News Blocks / Выборки записей новостей.
     *
     * Группа маршрутов к управлению некоторыми выборками новостей,
     * например новости конкретного пользователя или контент для сайдбаров.
     *
     * Данные маршруты относятся к префиксу /news/
     */

    Route::group(['prefix' => 'your-news'], function () {
        /**
         * Get all articles by specific user / Вывести список всех новостей определенного пользователя.
         * POST news/your-news {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,cost,isEditable,title}, {...}]
         */
        Route::post('/', 'NewsBlocksController@getYourArticles')
            ->name('news.newsBlocks.yourNews');
        /**
         * Get all confirmed articles by specific user / Вывести список всех подтвержденных новостей определенного пользователя.
         * POST news/your-news/confirmed {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,cost,isEditable,title}, {...}]
         */
        Route::post('confirmed', 'NewsBlocksController@getYourConfirmedArticles')
            ->name('news.newsBlocks.yourConfirmedNews');
    });

    /**
     * Get latest articles list (with content included) / Вывести список последних новостей (с текстом включительно)
     * POST news/latest-news {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,isEditable,title,content}, {...}]
     */
    Route::post('latest', 'NewsBlocksController@getLatestArticles')
        ->name('news.newsBlocks.latestNews');
    /**
     * Get random category articles list (with content included) / Вывести список новостей с рандомной категории (с текстом включительно)
     * POST news/random-category {quantity, user_id}: JSON [{id,category,categorySlug,humanDate,country,rating,isConfirmed,isEditable,title,content}, {...}]
     */
    Route::post('random-category', 'NewsBlocksController@getRandomCategoryArticles')
        ->name('news.newsBlocks.randomCategoryNews');

    /**
     * News Rating / Управление рейтингом новостей.
     *
     * Группа маршрутов к управлению непосредственно рейтингом записей,
     * лайками, дизлайками etc.
     *
     * Данные маршруты относятся к префиксу /news/
     */

    /**
     * Get article rating / Получить рейтинг записи.
     * GET news/{id}/rating {}: JSON {rating: int value}
     */
    Route::get('{id}/rating', 'NewsRatingController@getRating')
        ->name('news.ratingResult');
    /**
     * Is article liked by user / Лайкнута ли эта запись этим пользователем.
     * POST news/{id}/is-liked {user_id}: JSON {message: bool}
     */
    Route::post('{id}/is-liked', 'NewsRatingController@isArticleLikedByUser')
        ->name('news.isLiked');
    /**
     * Is article disliked by user / Дизлайкнута ли эта запись этим пользователем.
     * POST news/{id}/is-disliked {user_id}: JSON {message: bool}
     */
    Route::post('{id}/is-disliked', 'NewsRatingController@isArticleDislikedByUser')
        ->name('news.isDisliked');
    /**
     * [Action] Like article / Лайкнуть запись.
     * POST news/{id}/like {user_id}: msg
     */
    Route::post('{id}/like', 'NewsRatingController@likeArticle')
        ->name('news.likeAction');
    /**
     * [Action] Dislike article / Дизлайкнуть запись.
     * POST news/{id}/dislike {user_id}: msg
     */
    Route::post('{id}/dislike', 'NewsRatingController@dislikeArticle')
        ->name('news.dislikeAction');

}); // prefix => 'news'
