<?php

namespace Domain\Repositories\Exercises;

use App\Models\Exercise;
use Illuminate\Support\Facades\DB;

class ExerciseRepository implements ExerciseRepositoryInterface
{
    private Exercise $exerciseModel;

    /**
     * ExerciseRepository constructor.
     * @param Exercise $exerciseModel
     */
    public function __construct(Exercise $exerciseModel)
    {
        $this->exerciseModel = $exerciseModel;
    }

    /**
     * @param int $exercise_id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById(int $exercise_id)
    {
        return $this->exerciseModel->newQuery()->find($exercise_id);
    }

    /**
     * @param int $user_id
     * @param int $exercise_type_id
     * @param null $exercise_at
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(int $user_id, int $exercise_type_id, $exercise_at = null)
    {
        if ($exercise_at === null) {
            $exercise_at = now()->format('Y-m-d');
        }
        $last_exercise_in_day = $this->exerciseModel->newQuery()
            ->where('user_id', $user_id)
            ->whereDate('exercise_at', $exercise_at)
            ->orderBy('sort_index')
            ->get()->last();

        $sort_index = 1;
        if (!empty($last_exercise_in_day)) {
            $sort_index = $last_exercise_in_day->sort_index + 1;
        }

        return $this->exerciseModel->newQuery()->create(
            [
                'user_id' => $user_id,
                'exercise_type_id' => $exercise_type_id,
                'exercise_at' => $exercise_at,
                'volume' => 0,
                'max' => 0,
                'set' => 0,
                'sort_index' => $sort_index
            ]
        );
    }

    /**
     * @param int $user_id
     * @param int $exercise_type_id
     * @param null $date
     * @return mixed
     */
    public function findWithTargetDay(int $user_id, int $exercise_type_id, $date = null)
    {
        if ($date === null) {
            $date = now()->format('Y-m-d');
        }

        return $this->exerciseModel->newQuery()
            ->where('user_id', '=', $user_id)
            ->where('exercise_type_id', '=', $exercise_type_id)
            ->where(DB::raw("DATE_FORMAT(exercise_at, '%Y-%m-%d')"), '=', $date)
            ->get()
            ->first();
    }

    /**
     * @param int $user_id
     * @param null $date
     * @return mixed
     */
    public function getByDate(int $user_id, $date = null)
    {
        if ($date === null) {
            $date = now()->format('Y-m-d');
        }

        return $this->exerciseModel->newQuery()
            ->where('user_id', '=', $user_id)
            ->where(DB::raw("DATE_FORMAT(exercise_at, '%Y-%m-%d')"), '=', $date)
            ->orderBy('sort_index')
            ->get();
    }

    /**
     * @param int $user_id
     * @param string $start_date
     * @param string $end_date
     * @return mixed|void
     */
    public function getTargetPeriodExerciseVolume(int $user_id, string $start_date, string $end_date)
    {
        return DB::table('body_parts')
            ->select('body_parts.id', 'body_parts.name', DB::raw("SUM(exercises.volume) as volume"))
            ->join('exercise_types', 'exercise_types.body_part_id', '=', 'body_parts.id')
            ->leftJoin('exercises', 'exercises.exercise_type_id', '=', 'exercise_types.id')
            ->where('exercises.user_id', $user_id)
            ->whereBetween('exercises.exercise_at', [$start_date, $end_date])
            ->groupBy('body_parts.id')
            ->get();
    }

    /**
     * 曜日ごと日曜始まり
     * @param int $user_id
     * @param string|null $start_date
     * @param string $end_date
     * @return mixed|void
     */
    public function getWeeklyExerciseVolume(int $user_id, string $start_date = null, string $end_date)
    {
        return DB::table('exercises')
            ->select(
                'body_parts.id as body_part_id',
                'body_parts.name as body_part_name',
                "SUM(exercises.volume) as weekly_volume",
                DB::raw("SUBDATE(DATE_FORMAT(exercises.exercise_at, '%y-%m-%d'), WEEKDAY(exercises.exercise_at)+1) AS beginning")
            )->join('exercise_types', 'exercises.exercise_type_id', '=', 'exercise_types.id')
            ->join('body_parts', 'exercise_types.body_part_id', '=', 'body_pars.id')
            ->where('exercises.user_id', $user_id)
            ->whereBetween('exercises.exercise_at', [$start_date, $end_date])
            ->groupBy('body_parts.id')
            ->groupBy('beginning')
            ->orderBy('beginning', 'DESC')
            ->get();
    }

    public function sortExercise(array $exercises)
    {
        foreach ($exercises as $exercise) {
            $this->exerciseModel->newQuery()
                ->where(['id' => $exercise['exercise_id']])
                ->update(
                    [
                        'sort_index' => $exercise['update_sort_index']
                    ]
                );
        }
    }


}
