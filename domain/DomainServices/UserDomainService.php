<?php

namespace Domain\DomainServices;

use App\Models\Auth;
use Domain\Repositories\Users\UserRepositoryInterface;

class UserDomainService
{
    private UserRepositoryInterface $userRepository;

    /**
     * UserDomainService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isExist(Auth $auth) : bool
    {
        $user = $this->userRepository->findByAuthId($auth->id);
        $is_exist = false;

        if (!empty($user)) {
            $is_exist = true;
        }

        return $is_exist;
    }


}