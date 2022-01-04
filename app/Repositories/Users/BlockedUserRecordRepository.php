<?php

namespace App\Repositories\Users;

use App\Models\Users\BlockedUserRecord as Model;
use App\Repositories\CoreRepository;

/**
 * Class BlockedUserRecordRepository
 *
 * Данный класс работает со всеми сущностями класса BlockedUserRecord.
 * Здесь осуществляется поиск, выборка заблокированных пользователей
 * относительно текущего пользователя (блокиратора).
 *
 * @package App\Repositories\Users
 */
class BlockedUserRecordRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Check if author (potential blocked user) is already blocked.
     * Проверить, заблокирован ли потенциальный пользователь ($authorID).
     *
     * @param int $authorID
     * @param int $userID
     */
    public function getBlockedUserRecordByAuthorIDAndUserID($authorID, $userID)
    {
        $result = $this->startConditions()
            ->where('blocked_user_id', $authorID)
            ->where('user_id', $userID)
            ->first();

        return $result;
    }

    /**
     * Get list of blocked authors (for $userID).
     *
     * @param int $userID
     * @param int $quantity
     */
    public function getBlockedUsersByUserID($userID, $quantity)
    {
        $columns = [
            'id',
            'blocked_user_id'
        ];

        $result = $this->startConditions()
            ->select($columns)
            ->where('user_id', $userID)
            ->with(['blocked_user:id,username'])
            ->paginate($quantity); // set default value to "5"

        return $result;
    }
}
