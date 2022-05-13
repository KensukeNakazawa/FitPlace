<?php

namespace Tests\Unit\Domain\ApplicationServices\Exercise;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\ExerciseDetail;
use App\Models\DeletedExercise;
use App\Models\DeletedExerciseDetail;
use App\Models\ExerciseType;
use App\Models\PlanExercise;
use App\Models\PlanExerciseDetail;
use App\Models\WeekDay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;

class DeleteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $app;
    private $deleteExerciseService;
    private $userService;

    /**
     * ちゃんと削除されていること()
     */
    public function testDeleteExercise()
    {
        $this->deleteExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\DeleteService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $this->deleteExerciseService->deleteExercise($user, $exercise->id);

        $deleted_exercise = DeletedExercise::where(['exercise_id' => $exercise->id])->get()->first();
        $this->assertEquals($exercise->set, $deleted_exercise->set);
        $this->assertTrue(Exercise::where('id', $exercise->id)->get()->isEmpty());
        foreach ($exercise_details as $exercise_detail) {
            $deleted_exercise_detail = DeletedExerciseDetail::where(['exercise_detail_id' => $exercise_detail->id])->get()->first();
            $this->assertEquals($exercise_detail->rep, $deleted_exercise_detail->rep);
            $this->assertEquals($exercise_detail->weight, $deleted_exercise_detail->weight);
            $this->assertTrue(ExerciseDetail::where('id', $exercise_detail->id)->get()->isEmpty());
        }
    }

    /**
     * 他のユーザーのデータを消そうとした時にちゃんと例外が出るかどうか
     * データが消えていないか
     */
    public function testDeleteExerciseException()
    {
        $this->deleteExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\DeleteService');

        $name = 'testDeleteExerciseException';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testDeleteExerciseException@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $auth_fake = Auth::factory()->create(['email' => 'testDeleteExerciseException_fake@gmail.com', 'password' => 'pass']);
        $name_fake = 'fake_testDeleteExerciseException';
        $user_fake = User::factory()->create(['name' => $name_fake, 'birth_day' => $birth_day, 'auth_id' => $auth_fake->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise = Exercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'set' => 3]);
        $exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $exercise_details[] = ExerciseDetail::factory()->create(['exercise_id' => $exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage(__('messages.other_user_data.delete_failed'));
        $this->deleteExerciseService->deleteExercise($user_fake, $exercise->id);

        $this->assertEquals(Exercise::where('id', $exercise->id)->get(), $exercise);
        foreach ($exercise_details as $exercise_detail) {
            $this->assertEquals(ExerciseDetail::where('id', $exercise_detail->id)->get(), $exercise_detail);
        }
    }



    /**
     * ちゃんと削除されていること()
     */
    public function testDeletePlanExercise()
    {
        $this->deleteExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\DeleteService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testDeletePlanExercise@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $week_day = WeekDay::factory()->create(['name' => 'Sunday']);
        $plan_exercise = PlanExercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'week_day_id' => $week_day->id,  'set' => 3]);

        $plan_exercise_details = [];
        for ($index=0; $index < 3; $index++){
            $plan_exercise_details[] = PlanExerciseDetail::factory()->create(['plan_exercise_id' => $plan_exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $this->deleteExerciseService->deletePlanExercise($user, $plan_exercise->id);

        $this->assertTrue(Exercise::where('id', $plan_exercise->id)->get()->isEmpty());
        foreach ($plan_exercise_details as $plan_exercise_detail) {
            $this->assertTrue(ExerciseDetail::where('id', $plan_exercise_detail->id)->get()->isEmpty());
        }
    }

    /**
     * 他のユーザーのデータを消そうとした時にちゃんと例外が出るかどうか
     * データが消えていないか
     */
    public function testDeletePlanExerciseException()
    {
        $this->deleteExerciseService = $this->app->make('Domain\ApplicationServices\Exercise\DeleteService');

        $name = 'testDeletePlanExerciseException';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testDeleteExerciseException@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $auth_fake = Auth::factory()->create(['email' => 'testDeleteExerciseException_fake@gmail.com', 'password' => 'pass']);
        $name_fake = 'fake_testDeletePlanExerciseException';
        $user_fake = User::factory()->create(['name' => $name_fake, 'birth_day' => $birth_day, 'auth_id' => $auth_fake->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);
        $week_day = WeekDay::factory()->create(['name' => 'Sunday']);

        $plan_exercise = PlanExercise::factory()->create(['exercise_type_id' => $exercise_type->id, 'user_id' => $user->id, 'week_day_id' => $week_day->id,  'set' => 3]);
        $plan_exercise_details = [];

        for ($index=0; $index < 3; $index++){
            $plan_exercise_details[] = PlanExerciseDetail::factory()->create(['plan_exercise_id' => $plan_exercise->id, 'rep' => 10, 'weight' => 10]);
        }

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage(__('messages.other_user_data.delete_failed'));
        $this->deleteExerciseService->deletePlanExercise($user_fake, $plan_exercise->id);

        $this->assertEquals(Exercise::where('id', $plan_exercise->id)->get(), $plan_exercise);
        foreach ($plan_exercise_details as $plan_exercise_detail) {
            $this->assertEquals(ExerciseDetail::where('id', $plan_exercise_detail->id)->get(), $plan_exercise_detail);
        }
    }

}