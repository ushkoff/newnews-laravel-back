<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\EmailUserRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Repositories\Auth\PasswordResetRecordRepository;
use App\Repositories\Users\UserRepository;
use App\Traits\VerifyRecaptchaTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class PasswordResetRecordsController
 * @package App\Http\Controllers\Auth
 *
 * Класс с функционалом обновления пароля пользователя.
 * Реализованы функции:
 * 1) Создание запроса на обновление пароля и отправка письма на e-mail
 * 2) После перехода по ссылке из письма, проверка на срок действия токена
 * и редирект на страницу смены пароля
 * 3) Смена пароля на новый и сохранение в БД
 */
class PasswordResetRecordsController extends BaseController
{
    use VerifyRecaptchaTrait;

    /**
     * @var UserRepository
     * Репозиторий для доступа к специфическим полям сущности модели User в БД.
     */
    private $userRepository;

    /**
     * @var PasswordResetRecordRepository
     * Репозиторий для доступа к специфическим полям сущности модели PasswordReset в БД.
     */
    private $passwordResetRecordRepository;

    /**
     * ResetPasswordController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->userRepository = app(UserRepository::class);
        $this->passwordResetRecordRepository = app(PasswordResetRecordRepository::class);
    }

    /**
     * Create token password reset / Создать токен действия для сброса пароля.
     *
     * После запроса на сброс пароля идет следующее:
     * 1) Получение запроса типа EmailUserRequest
     * 2) Из него берется reCaptha токен и IP адрес
     * 3) Функция bool checkRecaptcha($token, $ip) проверяет правильность reCaptcha токена
     * 4) Нахождение записи данного пользователя в БД через данный e-mail
     * 5) Проверка: существует ли данный пользователь (с таким e-mail)
     * 6) Создать запись сброса пароля через passwordResetRepository->updateOrCreate()
     * или обновить её (время) в случае, если идет повторный запрос на сброс пароля.
     * В записи автоматически генерируется токен подтверждения.
     * 7) Отправить уведомление на e-mail типа PasswordResetRequestNotification,
     * в котором будет ссылка (с токеном), пройдя по которой, пользователь попадает
     * на метод find(), где проверяется не просрочен ли токен и идет перенаправление
     * на страницу смены пароля на новый.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(EmailUserRequest $request)
    {
        // Verify recaptcha
        $recaptcha_token = $request->recaptchaToken;
        $ip = $request->ip();
        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
        }

        $user = $this->userRepository->getUserByEmail($request->email);

        if (! is_null($user)) {
            $confirmedUserNotExist = ! $this->userRepository->isUserVerified($user->email);
        }

        if (is_null($user) || $confirmedUserNotExist) {
            return response()->json(['message' => 'A confirmed user with such email does not exist.'], 404);
        }

        $findToUpdate = ['email' => $request->email];
        $dataToCreate = [
            'email' => $user->email,
            'token' => Str::random(60)
        ];
        $passwordResetRecordInstance = $this->passwordResetRecordRepository
            ->updateOrCreate($findToUpdate, $dataToCreate);

        if ($user && $passwordResetRecordInstance) {
            $user->sendEmailPasswordResetRequest($passwordResetRecordInstance->token);

            return response()->json(['message' => 'A confirmation link sent to your email.'], 200);
        } else {
            return response()->json(['message' => 'Something went wrong...'], 500);
        }
    }

    /**
     * Find token password reset / Проверка токена записи восстановления пароля.
     *
     * После того, как пользователь перешел по ссылке на e-mail:
     * 1) Из GET запроса берется токен
     * 2) По нему находится или не находится запись восстановления пароля
     * 3) Проверяется время действия данной записи (по умолчанию 60 минут):
     * если токен просрочен, то запись удаляется
     * 4) В ином случае, пользователь перенаправляется на страницу смены пароля
     * (используя тот же токен в ссылке)
     *
     * @param $token
     */
    public function find($token)
    {
        $passwordResetRecordInstance = $this->passwordResetRecordRepository->findByToken($token);

        $instanceNotExists = !$passwordResetRecordInstance;
        if ($instanceNotExists) {
            return response()->json(['message' => 'This password reset token is invalid.'], 404);
        }

        $tokenIsOverdue = Carbon::parse($passwordResetRecordInstance->updated_at)
            ->addMinutes(config('auth.passwords.users.expire'))
            ->isPast();

        if ($tokenIsOverdue) {
            $passwordResetRecordInstance->delete();
            return response()->json([
                'message' => 'This password reset token is overdue. Try to reset password again.'
            ], 404);
        }

        // redirect to frontend
        $url = config('app.frontend_url') . 'reset-password/' . $passwordResetRecordInstance->token;
        return redirect($url);

        // return response()->json(['token' => $token], 200);
    }

    /**
     * Reset password / Сброс пароля.
     *
     * После того, как пользователь перешел на страницу смены пароля на новый
     * и заполнил соответствующую форму, данные из неё попадают сюда:
     * 1) Получение запроса $request типа PasswordResetRequest
     * 2) Из него берется reCaptha токен и IP адрес
     * 3) Функция bool checkRecaptcha($recaptchaToken, $ip) проверяет правильность reCaptcha токена
     * 4) По полученному e-mail определяется пользователь с БД
     * 5) Проверка: совпадает ли текущий e-mail с e-mail, к которому привязан данный токен смены пароля
     * через метод findByEmailAndToken() из репозитория PasswordResetRecordRepository
     * 6) Если всё хорошо, пароль переписывается на новый (из запроса)
     * 7) Удаляется данная запись восстановления пароля
     * 8) Отправляется уведомление типа PasswordResetSuccessNotification, о том, что пароль изменён успешно!
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(PasswordResetRequest $request)
    {
        // Verify recaptcha
        $recaptcha_token = $request->recaptchaToken;
        $ip = $request->ip();
        $isRecaptchaVerified = $this->checkRecaptcha($recaptcha_token, $ip);
        if (config('recaptcha.enabled') && !$isRecaptchaVerified) {
            return response()->json([ 'message' => 'Captcha is invalid.' ], 400);
        }

        $user = $this->userRepository->getUserByEmail($request->email);
        $userNotExists = !$user;
        if ($userNotExists) {
            return response()->json(['message' => 'A user with such email does not exist.'], 404);
        }

        $passwordResetRecordInstance = $this->passwordResetRecordRepository
            ->findByEmailAndToken($request->email, $request->token);

        $instanceNotExists = !$passwordResetRecordInstance;
        if ($instanceNotExists) {
            return response()->json(['message' => 'There is no password-reset request for such user.'], 404);
        }

        // Check if token is not expired yet
        $isTokenExpired = $this->find($request->token);
        if ($isTokenExpired->getStatusCode() == 404) {
            return response()->json([
                'message' => 'This password reset token is overdue. Try to reset password again.'
            ], 404);
        }

        $user->password = \Hash::make($request->password);
        $user->save();

        $passwordResetRecordInstance->delete();

        $user->sendEmailPasswordResetSuccess();

        return response()->json(['message' => 'You have successfully changed your password!'], 200);
    }
}
