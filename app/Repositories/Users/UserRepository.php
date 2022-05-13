<?php

namespace App\Repositories\Users;

use App\Models\User;
use Domain\Repositories\Users\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private User $model;

    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUsersById(array $user_ids)
    {
        return $this->model->newQuery()->where('id', $user_ids)->get();
    }

    public function findByAuthId(int $auth_id)
    {
        return $this->model->newQuery()->where('auth_id', $auth_id)->get()->first();
    }


}