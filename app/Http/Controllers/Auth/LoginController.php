<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\Users\UserRepository;
use App\Traits\VerifyRecaptchaTrait;
use Illuminate\Http\Request;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 *
 * Клас содержит метод Входа (Log In / Sign In) пользователя login(Request $request)
 * и метод Выхода (Log out) пользователя logout().
 * Верификация Google ReCaptcha.
 * Проверка: подтвержден ли аккаунт пользователя.
 * Аутентификация пользователя.
 * Создание сессии авторизации через oAuth2 (using Laravel Passport).
 * Завершение сессии (удаление токена и Log out).
 */
class LoginController extends BaseController
{
    /**
     * Трейт с функцией bool checkRecaptcha($token, $ip)
     */
    use VerifyRecaptchaTrait;

    /**
     * @var UserRepository
     *
     * Репозиторий для доступа к специфическим полям сущности модели User в БД.
     */
    private $userRepository;

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Login method / Метод Входа.
     *
     * 1) Принимается запрос типа стандартный Request
     * 2) Из него берется reCaptha токен и IP адрес
     * 3) Функция bool checkRecaptcha($token, $ip) проверяет правильность reCaptcha токена
     * 4) Проверка за полученным e-mail: существует ли пользователь с таким e-mail
     * 5) Проверка: подтвержден ли аккаунт пользователя (после регистрации через e-mail.)
     * 6) POST запрос на ${APP_URL}/oauth/token для получения токена сессии авторизации:
     *      1. Аутентификация через e-mail - password. В противном случае - исключение.
     *      2. Выдача токена. В противном случае - ошибка сервера ("Something went wrong").
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Psr\Http\Message\StreamInterface
     */
    public function login(LoginRequest $request)
    {
        // Verify recaptcha
        $recaptcha_token = $request->recaptchaToken;
        $ip = $request->ip();

        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
        }

        // Guzzle http client to request ${APP_URL}/oauth/token
        $http = new \GuzzleHttp\Client;

        $email = $request->email;

        // Check if such user exists
        $user = $this->userRepository->getUserByEmail($email);
        $userNotExists = !$user;
        if ($userNotExists) {
            return response()->json(['message' => 'A user with such email does not exist.'], 404);
        }

        // Check if user's account is not actived
        $userNotConfirmed = ! $this->userRepository->isUserVerified($email);
        if ($userNotConfirmed) {
            return response()
                ->json(['message' => 'Please confirm the registration in the letter we sent.'], 403);
        }

        /**
         * OAuth2 token retrieving (through Laravel Passport)
         */
        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->email, // using email as login
                    'password' => $request->password,
                    'scope' => ''
                ]
            ]);

            return $response->getBody();

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {

            $wrongPassword = !\Hash::check($request->password, $user->password);
            if ($wrongPassword) {
                return response()->json(['message' => 'Login or password is wrong.'], 401);
            }

            return response()->json(['message' => 'Login or password is wrong...'], $e->getCode());
        }
    }

    /**
     * Logout method / Метод Выхода.
     *
     * Удаление токена(-ов) сессии авторизации у текущего пользователя.
     */
    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully.', 200);
    }
}
