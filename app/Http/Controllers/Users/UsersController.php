<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SetUserSettingsRequest;
use App\Http\Resources\Users\GetUserDataResource;
use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;

/**
 * Class UsersController
 *
 * Данный класс работает со всеми сущностями класса User.
 *
 * @package App\Http\Controllers\Users
 */
class UsersController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Get current user's data.
     *
     * @param Request $request
     * @return GetUserDataResource
     */
    public function getCurrentUserData(Request $request)
    {
        $userData = auth()->guard('api')->user();

        return new GetUserDataResource($userData);
    }

    /**
     * Change user's settings...
     *
     * 1) Get news confirmation email or not.
     *
     * @param SetUserSettingsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeSettings(SetUserSettingsRequest $request)
    {
        $userID = $request->user_id;
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

//        $user = $this->userRepository->getUserByID($userID);
//
//        $newsConfirmNotice = $request->get_confirmation_email;
//
//        if (isset($newsConfirmNotice)) { // if == 1
//            $user->news_confirm_notice = $newsConfirmNotice;
//            $user->save();
//        }

        return response()->json(['message' => 'Success'], 200);
    }
}
