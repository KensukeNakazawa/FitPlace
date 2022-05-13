<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;

use App\Services\AuthService;
use App\Services\User\UserService;
use App\Services\Exercise\ExerciseTypeService;


class MeController extends Controller
{
    private AuthService $authService;
    private ExerciseTypeService $exerciseTypeService;
    private UserService $userService;

    /**
     * MeController constructor.
     * @param AuthService $authService
     * @param ExerciseTypeService $exerciseTypeService
     * @param UserService $userService
     */
    public function __construct(
        AuthService $authService,
        ExerciseTypeService $exerciseTypeService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->exerciseTypeService = $exerciseTypeService;
        $this->userService = $userService;
    }


    /**
     * ログインしているユーザーの取得
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request) : JsonResponse
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        return response()->json($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMeAuth(Request $request)
    {
        $auth = $this->getAuth($request);

        return response()->json($auth);
    }

    /**
     * ログインしているユーザーのトレーニング種目を取得
     * @param Request $request
     * @return JsonResponse
     */
    public function exerciseType(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $body_parts = $this->exerciseTypeService->getExerciseType($user->id);

        return response()->json($body_parts);
    }

    /**
     * 過去のトレーニングを取得する
     * @param Request $request
     * @param $exercise_type_id
     */
    public function pastExercise(Request $request, $exercise_type_id)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $page = $request->page;

        $result = $this->exerciseTypeService->getPastExercise($user, $exercise_type_id, $page);

        return response()->json(
            [
                'exercise_type' => $result['exercise_type'],
                'exercises' => $result['exercises']
            ]
        );
    }

    /**
     * パスワードを変更する
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;

        if ($new_password !== $confirm_password) {
            abort(403, __('messages.password_confirm_failed'));
        }

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->authService->changePassword($user, $old_password, $new_password);

        return response()->json(['message' => 'OK']);

    }
}

