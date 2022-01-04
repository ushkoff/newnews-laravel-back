<?php

namespace App\Repositories\Users;

use App\Models\Users\User as Model;
use App\Repositories\CoreRepository;

/**
 * Class UserRepository
 *
 * Данный класс работает со всеми сущностями класса User.
 *
 * @package App\Repositories\Users
 */
class UserRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Get user by ID.
     *
     * @param  int $id
     */
    public function getUserByID($id)
    {
        return $this->startConditions()
            ->find($id);
    }

    /**
     * Get user by email.
     *
     * @param  string $email
     */
    public function getUserByEmail($email)
    {
        return $this->startConditions()
            ->whereEmail($email)
            ->first();
    }

    /**
     * Check if user verified (by his/her email).
     *
     * If column 'email_verified_at' is not empty -> verified
     *
     * @param  string $email
     */
    public function isUserVerified($email)
    {
        return $this->startConditions()
            ->whereEmail($email)
            ->where('email_verified_at', '!=', null)
            ->exists();
    }

    /**
     * Get user's blocklist by his/her ID.
     *
     * >>> blocklist = [1,2,3...],
     * where 1,2,3,... - IDs of blocked users
     * (articles by them will be hidden).
     *
     * @param $id
     * @return array
     */
    public function getUserBlocklistByID($id)
    {
        $result = $this->startConditions()
            ->select('blocklist')
            ->find($id);

        $blocklist = json_decode($result->blocklist); // decode from '[...]' to [...]

        return $blocklist;
    }
}
