<?php

namespace App\Http\Controllers\Users;

use App\Http\Requests\Auth\UserRequest;
use App\Http\Requests\Users\ManageBlockedUserRecordRequest;
use App\Http\Resources\Users\GetBlocklistResource;
use App\Models\Users\BlockedUserRecord;
use App\Repositories\Users\BlockedUserRecordRepository;
use App\Repositories\Users\UserRepository;

/**
 * Class BlockedUserRecordsController
 *
 * Данный класс работает со всеми сущностями класса BlockedUserRecord.
 * Осуществляется управление списком заблокированных пользователей (authors)
 * текущим пользователем (user).
 *
 * @package App\Http\Controllers\Users
 */
class BlockedUserRecordsController extends BaseController
{
    /**
     * @var BlockedUserRecordRepository
     */
    private $blockedUserRecordRepository;

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

        $this->blockedUserRecordRepository = app(BlockedUserRecordRepository::class);
        $this->userRepository = app(UserRepository::class);
    }

    /**
     * Get user's blocklist (list of blocked authors).
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getUserBlocklist(UserRequest $request)
    {
        if ($request->user()->id != $request->user_id) {
            abort(500);
        }

        $quantity = $request->quantity;
        $userID = $request->user_id;

        $blocklist = $this->blockedUserRecordRepository->getBlockedUsersByUserID($userID, $quantity);

        return GetBlocklistResource::collection($blocklist);
    }

    /**
     * Add article's author to the user's blocklist.
     * Добавить автора статьи в список заблокированных пользователей.
     *
     * @param ManageBlockedUserRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function blockUser(ManageBlockedUserRecordRequest $request)
    {
        if ($request->user()->id != $request->user_id) {
            abort(500);
        }

        $authorID = $request->blocked_user_id; // ID of author, that current user wants to block
        $userID = $request->user_id; // actually user

        // User cannot block himself.
        if ($authorID === $userID) {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }

        // Check if this author is already blocked.
        $isAuthorAlreadyBlocked = $this->blockedUserRecordRepository
            ->getBlockedUserRecordByAuthorIDAndUserID($authorID, $userID);
        if ($isAuthorAlreadyBlocked) {
            return response()->json(['message' => 'This author is already blocked.'], 500);
        }

        // Else
        $user = $this->userRepository->getUserByID($userID);

        // Add author ID in "blocklist" user's column
        $listOfBlockedUsersIDs = json_decode($user->blocklist);
        array_push($listOfBlockedUsersIDs, intval($authorID));

        $user->blocklist = $listOfBlockedUsersIDs; // save it in "users" table
        $user->save();

        $data = [
          'blocked_user_id' => $authorID,
          'user_id'   => $userID
        ];
        BlockedUserRecord::create($data); // save it in "blocked_user_records" table

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Remove article's author from the user's blocklist.
     * Убрать автора статьи из списка заблокированных пользователей.
     *
     * @param ManageBlockedUserRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblockUser(ManageBlockedUserRecordRequest $request)
    {
        if ($request->user()->id != $request->user_id) {
            abort(500);
        }

        $authorID = $request->blocked_user_id; // ID of author, that current user wants to unblock
        $userID = $request->user_id; // actually user

        // User cannot block/unblock himself.
        if ($authorID === $userID) {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }

        // Get record with these users
        $blockedUserRecord = $this->blockedUserRecordRepository
            ->getBlockedUserRecordByAuthorIDAndUserID($authorID, $userID);

        if (! is_null($blockedUserRecord)) {
            $user = $this->userRepository->getUserByID($userID);

            // Remove author ID from "blocklist" user's column
            $listOfBlockedUsersIDs = json_decode($user->blocklist);
            $key = array_search(intval($authorID), $listOfBlockedUsersIDs);
            array_splice($listOfBlockedUsersIDs, $key, 1);

            $user->blocklist = $listOfBlockedUsersIDs; // save it in "users" table
            $user->save();

            $blockedUserRecord->delete(); // delete this record from "blocked_user_records" table
        } else {
            return response()->json(['message' => 'Something went wrong...'], 500);
        }

        return response()->json(['message' => 'Success'], 200);
    }
}
