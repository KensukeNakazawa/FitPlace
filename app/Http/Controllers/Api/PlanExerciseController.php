<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlanExercise\SortRequest;
use Illuminate\Http\Request;

use App\Http\Requests\Api\PlanExercise\StoreRequest;
use App\Http\Requests\Api\PlanExercise\UpdateRequest;

use App\Services\AuthService;
use Domain\ApplicationServices\Exercise\PlanExerciseRegisterService;
use Domain\ApplicationServices\Exercise\PlanExerciseService;
use Domain\ApplicationServices\Exercise\DeleteService;

class PlanExerciseController extends Controller
{
    private AuthService $authService;
    private PlanExerciseService $planExerciseService;
    private PlanExerciseRegisterService $planExerciseRegisterService;
    private DeleteService $exerciseDeleteService;

    /**
     * PlanExerciseController constructor.
     * @param AuthService $authService
     * @param PlanExerciseService $planExerciseService
     * @param PlanExerciseRegisterService $planExerciseRegisterService
     * @param DeleteService $exerciseDeleteService
     */
    public function __construct(
        AuthService $authService,
        PlanExerciseService $planExerciseService,
        PlanExerciseRegisterService $planExerciseRegisterService,
        DeleteService $exerciseDeleteService
    ) {
        $this->authService = $authService;
        $this->planExerciseService = $planExerciseService;
        $this->planExerciseRegisterService = $planExerciseRegisterService;
        $this->exerciseDeleteService = $exerciseDeleteService;
    }


    /**
     * 曜日ごとのトレーニング予定を取得する
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $result = $this->planExerciseService->getPlanExerciseWithWeekDay($user);

        return response()->json($result);
    }

    /**
     * ログインユーザーの対象の曜日でのトレーニング予定を取得
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlanExercise(Request $request)
    {
        $week_day_id = $request->week_day_id;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $result = $this->planExerciseService->getPlanExercise($user, $week_day_id);

        return response()->json($result);
    }

    /**
     * トレーニングプランを登録する
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $exercise_type_id = $request->exercise_type_id;
        $exercise_details = $request->exercise_details;
        $week_day_id = $request->week_day_id;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->planExerciseService->addPlanExercise(
            $user, $week_day_id, $exercise_type_id, $exercise_details
        );

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * トレーニング予定を変更するために、対象のトレーニング予定を取得
     * @param Request $request
     * @param $exercise_plan_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($exercise_plan_id)
    {
        $exercise_plan_id = (int) $exercise_plan_id;

        $result = $this->planExerciseService->getExerciseForEdit($exercise_plan_id);

        return response()->json($result);
    }

    /**
     * トレーニングプランを更新する
     * @param UpdateRequest $request
     * @param $exercise_plan_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateRequest $request, $exercise_plan_id)
    {
        $exercise_details = $request->exercise_details;
        $exercise_plan_id = (int) $exercise_plan_id;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->planExerciseService->updatePlanExercise($user, $exercise_plan_id, $exercise_details);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 予定のトレーニングをソートする
     * @param SortRequest $request
     * @param $week_day_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sortExercisePlan(SortRequest $request, $week_day_id)
    {
        $exercises =$request->exercises;

        $this->planExerciseService->sortPlanExercise($week_day_id, $exercises);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 対象のトレーニング予定を削除する
     * @param Request $request
     * @param $plan_exercise_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $plan_exercise_id)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->exerciseDeleteService->deletePlanExercise($user, $plan_exercise_id);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * 対象の曜日のトレーニング予定を今日のトレーニングに追加
     * @param Request $request
     */
    public function addPlanExercise(Request $request)
    {
        $week_day_id = $request->week_day_id;

        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $this->planExerciseRegisterService->register($user, $week_day_id);

        return response()->json(['message' => '登録できました！']);
    }
}
