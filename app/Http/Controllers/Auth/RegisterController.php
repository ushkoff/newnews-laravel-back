<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Users\User;
// use App\Traits\VerifyRecaptchaTrait;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 *
 * Клас содержит метод регистрации пользователя register(RegisterRequest $request):
 * Верификация Google ReCaptcha.
 * Занесение информации пользователя в БД.
 * Отправка письма подтверждения регистрации на e-mail.
 */
class RegisterController extends BaseController
{
    /**
     * Трейт с функцией bool checkRecaptcha($token, $ip)
     */
    // use VerifyRecaptchaTrait;

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * User registration / Регистрация пользователя
     *
     * 1) Принимается запрос типа RegisterRequest (см. App\Http\Requests\Auth\RegisterRequest)
     * 2) Из него берется reCaptha токен и IP адрес
     * 3) Функция bool checkRecaptcha($token, $ip) проверяет правильность reCaptcha токена
     * 4) Формирование массива с данными нового пользователя и занесение его в БД (таблица users)
     * 5) Отправка подтверждения регистрации на e-mail функцией sendApiEmailVerificationNotification()
     * (см. App\Models\User\User -> см. App\Notifications\Auth\VerifyApiEmailNotification)
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
//        // Verify recaptcha
//        $recaptcha_token = $request->recaptchaToken;
//        $ip = $request->ip();
//        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
//        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
//            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
//        }

        $data = [
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => \Hash::make($request->password),
            'country'      => $request->country,
            'country_code' => $request->countryCode,
            'timezone'     => $request->timezone
        ];

        $user = User::create($data);

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'A confirmation link sent to your email.'], 200);
    }
}
