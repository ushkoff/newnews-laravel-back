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
| is assigned the "api" middleware group. Enjoy building your API! thx bro.
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
 * Auth
 *
 * Маршруты к методам контроллеров пространства имен Auth.
 * Данные маршруты имеют префикс /auth/
 */
Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
    /**
     * User register / Регистрация пользователя.
     * POST auth/register/ {username, email, password, country, countryCode, timezone, recaptchaToken}: msg
     */
    Route::post('register', 'RegisterController@register')
        ->name('auth.register');

    /**
     * Login / Вход.
     * POST auth/login/ {email, password, recaptchaToken}: {Bearer access_token, expires_in}
     */
    Route::post('login', 'LoginController@login')
        ->name('auth.login');

    /**
     * Группа маршрутов с пространства имен Auth для авторизованных пользователей.
     */
    Route::group(['middleware' => 'auth:api'], function () {
        /**
         * Logout / Выход.
         * POST auth/logout/ {}: msg; Header "Authorization": "Bearer {access_token}"
         */
        Route::post('logout', 'LoginController@logout')
            ->name('auth.logout');
    }); // 'middleware' => 'auth:api'

    /**
     * Email account confirmation / Подтверждение аккаунта по e-mail.
     */
    Route::group(['prefix' => 'email'], function () {
        /**
         * Verify user / Обозначить аккаунт пользователя как подтвержденный.
         * GET auth/email/verify/{userID}: msg
         */
        Route::get('verify/{id}', 'EmailVerificationController@verify')
            ->name('auth.verification.verify')->middleware('signed');
        /**
         * Resend email verification notification / Повторно отправить письмо подтверждения аккаунта пользователя.
         * GET auth/email/resend/{email}: msg
         */
        Route::get('resend/{email}', 'EmailVerificationController@resend')
            ->name('auth.verification.resend');
    }); // 'prefix' => 'email'

    /**
     * Reset password / Сброс пароля.
     *
     * Группа маршрутов к методам сброса пароля.
     * Данные маршруты имеют префикс /reset-password/
     */
    Route::group(['prefix' => 'password-reset'], function () {
        /**
         * Create password reset record / Сделать запись сброса пароля.
         * POST auth/reset-password/create/ {email, recaptchaToken}: msg
         */
        Route::post('create', 'PasswordResetRecordsController@create')
            ->name('auth.resetPassword.create');
        /**
         * Check record token / Проверить не просрочен ли токен записи сброса пароля.
         * GET auth/reset-password/find/{token}/ {} : msg
         */
        Route::get('find/{token}', 'PasswordResetRecordsController@find')
            ->name('auth.resetPassword.find');
        /**
         * Reset password / Сброс текущего пароля пользователя и замена его на новый.
         * POST auth/reset-password/reset/ {email, password, token, recaptchaToken}: msg
         */
        Route::post('reset', 'PasswordResetRecordsController@reset')
            ->name('auth.resetPassword.reset');
    });
}); // 'prefix' => 'auth'

/**
 * User
 *
 * Маршруты к методам контроллеров пространства имен User.
 * Данные маршруты имеют префикс /user/
 * Необходима авторизация.
 */
Route::group(['namespace' => 'Users', 'prefix' => 'user'], function () {

    Route::group(['middleware' => 'auth:api'], function () {
        /**
         * Get current user's data / Получить информацию о текущем пользователе.
         * GET user/current {through auth layer}: JSON {id,username,email,newsNumber,verifiedNewsNumber,country,countryCode,timezone,dateRegistered,getConfirmationEmail}
         */
        Route::get('/current', 'UsersController@getCurrentUserData')
            ->name('user.current');
        /**
         * Change user's settings / Поменять настройки пользователя.
         * POST user/settings {user_id, get_confirmation_email}: msg
         */
        Route::post('settings', 'UsersController@changeSettings')
            ->name('user.settings');
    }); // 'middleware' => 'auth:api'


    /**
     * Blocklist / Список заблокированных пользователей
     *
     * Маршруты к методам контроллера BlockedUserRecordsController.
     * Данные маршруты имеют префикс user/blocklist/
     * Необходима авторизация.
     */
    Route::group(['prefix' => 'blocklist', 'middleware' => 'auth:api'], function () {
        /**
         * Get user's blocklist / Получить список заблокированных пользователем авторов.
         * POST user/blocklist {user_id, quantity}: JSON [{id,blocked_user_id,blocked_user_username}, ...]
         */
        Route::post('/', 'BlockedUserRecordsController@getUserBlocklist')
            ->name('user.blocklistResults');
        /**
         * [Action] Block article's author  / Заблокировать автора статьи
         * POST user/blocklist/add {user_id,blocked_user_id}: msg
         */
        Route::post('add', 'BlockedUserRecordsController@blockUser')
            ->name('user.blocklist.add');
        /**
         * [Action] Unblock article's author  / Разблокировать автора статьи
         * POST user/blocklist/remove {user_id,blocked_user_id}: msg
         */
        Route::post('remove', 'BlockedUserRecordsController@unblockUser')
            ->name('user.blocklist.remove');
    }); // prefix => 'blocklist', 'middleware' => 'auth:api'
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
     * Частично необходима авторизация.
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
         * POST news/global-news/{id} {user_id}: JSON {id,category,categoryID,categorySlug,userID,username,date,isEdited,isEditable,country,countryCode,refs,author_pubkey,signature,rating,isConfirmed,title,content}
         */
        Route::post('{id}', 'ArticlesController@show')
            ->name('news.globalNews.show');

        Route::group(['middleware' => 'auth:api'], function () {
            /**
             * Create article / Создание записи.
             * POST news/global-news/store {category_id,user_id,title,content_html,country,country_code,author_pubkey,signature,refs,recaptchaToken}: msg
             */
            Route::post('store', 'ArticlesController@store')
                ->name('news.globalNews.store');

            /**
             * Update article / Редактирование записи.
             * POST news/global-news/{id}/update {user_id,category_id,title,content_html,refs,recaptchaToken}: msg
             */
            Route::post('{id}/update', 'ArticlesController@update')
                ->name('news.globalNews.update');

            /**
             * Delete article / Удалить запись.
             * POST news/global-news/{id}/delete {user_id}: msg
             */
            Route::post('{id}/delete', 'ArticlesController@destroy')
                ->name('news.globalNews.destroy');
        }); // 'middleware' => 'auth:api'
    }); // prefix => 'global-news'

    /**
     * Local News / Локальные новости (>>> в пределах страны)
     *
     * Группа маршрутов к управлению локальными новостями.
     * Данные маршруты имеют префикс /local-news/
     * Необходима авторизация.
     */
    Route::group(['prefix' => 'local-news', 'middleware' => 'auth:api'], function () {
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
    }); // prefix => 'local-news', 'middleware' => 'auth:api'

    /**
     * News Blocks / Выборки записей новостей.
     *
     * Группа маршрутов к управлению некоторыми выборками новостей,
     * например новости конкретного пользователя или контент для сайдбаров.
     *
     * Данные маршруты относятся к префиксу /news/
     * Частично необходима авторизация.
     */

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

    Route::group(['prefix' => 'your-news', 'middleware' => 'auth:api'], function () {
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
    }); // 'prefix' => 'your-news', 'middleware' => 'auth:api'

    /**
     * News Rating / Управление рейтингом новостей.
     *
     * Группа маршрутов к управлению непосредственно рейтингом записей,
     * лайками, дизлайками etc.
     *
     * Данные маршруты относятся к префиксу /news/
     * Частично необходима авторизация.
     */

    /**
     * Get article rating / Получить рейтинг записи.
     * GET news/{id}/rating {}: JSON {rating: int value}
     */
    Route::get('{id}/rating', 'NewsRatingController@getRating')
        ->name('news.ratingResult');

    Route::group(['middleware' => 'auth:api'], function () {
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
    }); // 'middleware' => 'auth:api'
}); // prefix => 'news'

/**
 * Location
 *
 * Маршруты к методам контроллеров пространства имен Location.
 * Данные маршруты имеют префикс /location/
 */
Route::group(['namespace' => 'Location', 'prefix' => 'location'], function () {
    /**
     * Countries / Страны
     *
     * Маршруты к методам контроллера CountriesController.
     * Данные маршруты имеют префикс /countries/
     */
    Route::group(['prefix' => 'countries'], function () {
        /**
         * Get countries info list / Получить список стран (с информацией про них).
         * GET location/countries/ {}: JSON [{name,alpha2Code,timezones[]}, ...]
         */
        Route::get('/', 'CountriesController@index')
            ->name('location.countries.index');
        /**
         * Get country by country code / Найти страну по коду страны.
         * GET location/countries/alpha/{code}/ {}: JSON {name,alpha2Code,timezones[]}
         */
        Route::get('alpha/{code}', 'CountriesController@getCountryByCountryCode')
            ->name('location.countries.getByCountryCode');
    });
});
