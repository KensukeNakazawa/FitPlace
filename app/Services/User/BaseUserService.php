<?php

namespace App\Services\User;

use App\Models\User;

class BaseUserService
{
    protected User $userModel;

    /**
     * BaseUserService constructor.
     * @param User $userModel
     */
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

}