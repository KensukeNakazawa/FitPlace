<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\User\CreateUserRequest;

use App\Services\User\UserService;

class UserController extends Controller
{
    private UserService $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * ユーザーを作成する
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function createUser(CreateUserRequest $request)
    {
        $name = $request->name;
        $birth_day = $request->birth_day;
        $auth = $this->getAuth($request);

        $this->userService->createUser($name, $birth_day, $auth);
        return response()->json(['message'=>'OK']);
    }

}
