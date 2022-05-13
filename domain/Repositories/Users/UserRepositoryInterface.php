<?php

namespace Domain\Repositories\Users;

interface UserRepositoryInterface
{
    public function getUsersById(array $user_ids);

    public function findByAuthId(int $auth_id);
}