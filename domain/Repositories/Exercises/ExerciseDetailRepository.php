<?php

namespace Domain\Repositories\Exercises;

use App\Models\ExerciseDetail;

class ExerciseDetailRepository implements ExerciseDetailRepositoryInterface
{
    private ExerciseDetail $exerciseDetailModel;

    /**
     * ExerciseDetailRepository constructor.
     * @param ExerciseDetail $exerciseDetailModel
     */
    public function __construct(ExerciseDetail $exerciseDetailModel)
    {
        $this->exerciseDetailModel = $exerciseDetailModel;
    }

    public function create(int $exercise_id, int $rep=null, float $weight=null)
    {
        if ($rep === null) {
            $rep = 0;
        }
        if ($weight === null) {
            $weight = 0;
        }

        return $this->exerciseDetailModel->newQuery()->create(
            [
                'exercise_id' => $exercise_id,
                'rep' => $rep,
                'weight' => $weight
            ]
        );
    }

    public function update(int $exercise_detail_id, int $rep = null, float $weight = null)
    {
        if ($rep === null) {
            $rep = 0;
        }
        if ($weight === null) {
            $weight = 0;
        }

        $exercise_detail = $this->exerciseDetailModel->newQuery()->find($exercise_detail_id);

        $exercise_detail->rep = $rep;
        $exercise_detail->weight = $weight;
        $exercise_detail->save();
        return $exercise_detail;
    }


}