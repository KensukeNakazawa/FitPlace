<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\BodyPart\StoreRequest;

use App\Services\AuthService;
use Domain\ApplicationServices\Exercise\ExerciseTypeService;

use App\Models\BodyPart;

class BodyPartController extends Controller
{
    private AuthService $authService;
    private BodyPart $bodyPartModel;
    private ExerciseTypeService $exerciseTypeService;

    /**
     * BodyPartController constructor.
     * @param AuthService $authService
     * @param BodyPart $bodyPartModel
     * @param ExerciseTypeService $exerciseTypeService
     */
    public function __construct(
        AuthService $authService,
        BodyPart $bodyPartModel,
        ExerciseTypeService $exerciseTypeService
    ) {
        $this->authService = $authService;
        $this->bodyPartModel = $bodyPartModel;
        $this->exerciseTypeService = $exerciseTypeService;
    }


    /**
     * 体の部位を返す
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        $body_parts = $this->bodyPartModel->newQuery()->get();

        return response()->json($body_parts);
    }

    /**
     * 筋トレの種目を追加する　
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function storeExerciseType(StoreRequest $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $body_part_id = $request->body_part_id;
        $exercise_type_name = $request->exercise_type_name;

        $exercise_type = $this->exerciseTypeService->addExerciseType(
            $user, $body_part_id, $exercise_type_name
        );

        return response()->json(
            [
                'exercise_type' => $exercise_type,
                'message' => '追加しました！'
            ]
        );
    }


}
