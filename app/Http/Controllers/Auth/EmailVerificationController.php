<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\Users\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class EmailVerificationController
 * @package App\Http\Controllers\Auth
 *
 * Класс содержит методы действия верификации пользователя,
 * что перешел по ссылке подтверждения регистрации на e-mail,
 * и метод повторной отправки письма подтверждения регистрации на e-mail.
 */
class EmailVerificationController extends BaseController
{
    /**
     * @var UserRepository
     * Репозиторий для доступа к специфическим полям сущности модели User в БД.
     */
    private $userRepository;

    /**
     * EmailVerificationApiController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Mark the authenticated user’s email address as verified / Обозначить аккаунт пользователя как подтвержденный.
     *
     * Действия после того, как пользователь нажал на ссылку подтверждения регистрации:
     * 1) Найти пользователя в БД за его ID.
     * 2) В поле `email_verified_at` вписать время подтверждения аккаунта через e-mail.
     * 3) Сохранить запись.
     * 4) Перенаправить на страницу Входа.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function verify(Request $request)
    {
        $userID = $request->id;
        $user = $this->userRepository->getUserByID($userID);

        $user->email_verified_at = Carbon::now();
        $user->save();

        // redirect to frontend
        $url = config('app.frontend_url') . 'login?verification=true';
        return redirect($url);

        // return response()->json(['message' => 'Email verified successfully! Redirecting...'], 200);

    }

    /**
     * Resend the email verification notification / Повторно отправить письмо подтверждения аккаунта пользователя.
     *
     * Действия после того, как пользователь нажал на кнопку "письмо не пришло":
     * 1) Найти пользователя в БД за его ID.
     * 2) Отправить такое же уведомление через метод sendEmailVerificationNotification().
     * 3) Сообщение: "Уведомление отправлено".
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $user = $this->userRepository->getUserByEmail($request->email);

        if (! is_null($user)) {
            $userIsConfirmed = $this->userRepository->isUserVerified($request->email);
            if ($userIsConfirmed) {
                return response()->json(['message' => 'User already has verified email!'], 422);
            }

            $user->sendEmailVerificationNotification();

            return response()->json(['message' => 'The notification has been resubmitted.'], 200);
        } else {
            return response()->json(['message' => 'Such user does not exist.'], 404);
        }
    }

}
