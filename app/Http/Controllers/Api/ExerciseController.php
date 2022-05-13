<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Exercise\SortRequest;
use Illuminate\Http\Request;

use App\Http\Requests\Api\Exercise\UpdateExerciseRequest;
use App\Http\Requests\Api\Exercise\StoreExerciseRequest;

use App\Services\AuthService;
use Domain\ApplicationServices\Exercise\ExerciseService;
use Domain\ApplicationServices\Exercise\DeleteService;

class ExerciseController extends Controller
{

    private AuthService $authService;
    private ExerciseService $exerciseService;
    private DeleteService $exerciseDeleteService;

    /**
     * ExerciseController constructor.
     * @param AuthService $authService
     * @param ExerciseService $exerciseService
     * @param DeleteService $exerciseDeleteService
     */
    public function __construct(
        AuthService $authService,
        ExerciseService $exerciseService,
        DeleteService $exerciseDeleteService
    ) {
        $this->authService = $authService;
        $this->exerciseService = $exerciseService;
        $this->exerciseDeleteService = $exerciseDeleteService;
    }


    /**
     * ログインユーザーの対象の日付でのトレーニングを取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function getExercise(Request $request)
    {

        $date = $request->date ? $request->date : null;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $result = $this->exerciseService->getExerciseForIndex($user, $date);

        return response()->json($result);
    }

    /**
     * トレーニングを編集するために、トレーニングを取得
     * @param $exercise_id
     * @return \Illuminate\Http\JsonResponse
     */
    final public function edit($exercise_id)
    {
        $exercise_id = (int) $exercise_id;
        $result = $this->exerciseService->getExerciseForEdit($exercise_id);
        return response()->json($result);
    }

    /**
     * 対象のトレーニングを更新する
     * @param Request $request
     * @param $exercise_id
     * @return \Illuminate\Http\JsonResponse
     */
    final public function updateExercise(UpdateExerciseRequest $request, $exercise_id)
    {
        $exercise_details = $request->exercise_details;
        $exercise_id = (int) $exercise_id;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->exerciseService->updateExercise($user, $exercise_id, $exercise_details);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * トレーニングをソートする
     * @param SortRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function sortExercise(SortRequest $request)
    {
        $exercises = $request->exercises;
        $this->exerciseService->sortExercise($exercises);
        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 対象のトレーニングを削除
     * @param Request $request
     * @param $exercise_id
     * @return \Illuminate\Http\JsonResponse
     */
    final public function deleteExercise(Request $request, $exercise_id)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->exerciseDeleteService->deleteExercise($user, $exercise_id);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 新しいトレーニングを追加する
     * @param StoreExerciseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeExercise(StoreExerciseRequest $request)
    {
        $exercise_type_id = $request->exercise_type_id;
        $exercises = $request->exercise_details;
        $date = $request->date;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->exerciseService->addExercise($user, $exercise_type_id, $exercises, $date);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 対象のトレーニング種目を取得する
     * @param Request $request
     * @param $exercise_type_id
     * @return \Illuminate\Http\JsonResponse
     */
    final public function getExerciseType($exercise_type_id)
    {
        $exercise_type_id = (int) $exercise_type_id;
        $exercise_type = $this->exerciseService->getExerciseType($exercise_type_id);

        return response()->json(
            [
                'exercise_type' => $exercise_type
            ]
        );
    }

    /**
     * 対象のユーザーが対象の日付、トレーニング種目をしているかの確認
     * @param Request $request
     */
    final public function checkExistExercise(Request $request)
    {
        $exercise_type_id = $request->exercise_type_id;
        $date = $request->date ? $request->date : null;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $exercise = $this->exerciseService->getExercise($user, $exercise_type_id, $date);

        return response()->json(
            [
                'exercise' => $exercise
            ]
        );
    }

    final public function getThisWeekExerciseVolume(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);
        $result = $this->exerciseService->getThisWeekExerciseVolume($user);

        return response()->json(
            [
                'exercise_volume' => $result
            ]
        );
    }
}
