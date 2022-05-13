<?php

namespace Domain\Repositories\Exercises;

interface ExerciseRepositoryInterface
{
    public function findById(int $exercise_id);

    public function create(int $user_id, int $exercise_type_id, $exercise_at = null);

    /**
     * ユーザーの当該トレーニングタイプのトレーニングで
     * 指定した日付のデータを取得、日付指定しない場合は当日
     * @param int $user_id
     * @param int $exercise_type_id
     * @param null $date
     * @return mixed
     */
    public function findWithTargetDay(int $user_id, int $exercise_type_id, $date = null);

    /**
     * 対象の日付でのトレーニングを取得
     * 日付指定なければ、当日
     * @param int $user_id
     * @param null $date
     * @return mixed
     */
    public function getByDate(int $user_id, $date = null);

    /**
     * 対象の範囲での種目ごとのボリュームを取得
     * @param int $user_id
     * @param string $start_date
     * @param string $end_date
     * @return mixed
     */
    public function getTargetPeriodExerciseVolume(int $user_id, string $start_date, string $end_date);

    /**
     * 対象の範囲内で週毎に各種目のボリュームを取得
     * @param int $user_id
     * @param string|null $start_date
     * @param string $end_date
     * @return mixed
     */
    public function getWeeklyExerciseVolume(int $user_id, string $start_date=null, string $end_date);

    /**
     * ソートする
     * @param array $exercises
     * @return mixed
     */
    public function sortExercise(array $exercises);
}
