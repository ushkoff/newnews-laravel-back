<?php

namespace  App\Repositories\Auth;

use App\Models\Auth\PasswordResetRecord as Model;
use App\Repositories\CoreRepository;

/**
 * Class PasswordResetRecordRepository
 *
 * Данный класс работает со всеми сущностями класса PasswordResetRecord.
 *
 * @package App\Repositories\Auth
 */
class PasswordResetRecordRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * Find "password-reset" record by given token.
     *
     * @param  string $token
     */
    public function findByToken($token)
    {
        return $this->startConditions()
            ->where('token', $token)
            ->first();
    }

    /**
     * Find "password-reset" record by user's email and token.
     *
     * @param  array $email
     * @param  array $token
     */
    public function findByEmailAndToken($email, $token)
    {
        return $this->startConditions()->where([
            ['email', $email],
            ['token', $token]
        ])->first();
    }

    /**
     * Create password change record.
     * Or update if exists one record for current email.
     *
     * @param $findToUpdate
     * @param $dataToCreate
     * @return mixed
     */
    public function updateOrCreate($findToUpdate, $dataToCreate)
    {
        return $this->startConditions()
            ->updateOrCreate($findToUpdate, $dataToCreate);
    }
}
