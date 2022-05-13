<?php

namespace Tests\Unit\Services\Exercise;

use App\Models\BodyPart;
use App\Models\ExerciseType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Auth;
use App\Models\User;

class ExerciseTypeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $app;
    private $exerciseTypeServise;
    private $userService;

    public function testGetExerciseType()
    {
        $this->exerciseTypeServise = $this->app->make('App\Services\Exercise\ExerciseTypeService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);
        $exercise_type = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => 'press', 'sort_index' => 1]);

        $exercise_types = $this->exerciseTypeServise->getExerciseType($user->id);

        $this->assertEquals($exercise_type->name, $exercise_types[0]['exercise_types'][0]['exercise_type_name']);
    }

    /**
     * 種目の追加、正常系
     */
    public function testAddExerciseType()
    {

        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseTypeService');

        $name = 'kensuke_test';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testGetExerciseType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        for ($i = 0; $i < 10; $i++) {
            $exercise_mame = "press" . $i;
            $sort_index = $i + 1;
            ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => $exercise_mame, 'sort_index' => $sort_index]);
        }

        $new_exercise_name = 'press_test';
        $exercise_type = $this->exerciseService->addExerciseType($user, $body_part->id, $new_exercise_name);

        $created_exercise_type = ExerciseType::find($exercise_type->id);

        $this->assertEquals($exercise_type->name, $created_exercise_type->name);
        $this->assertEquals($exercise_type->body_part_id, $created_exercise_type->body_part_id);
        $this->assertEquals($exercise_type->user_id, $created_exercise_type->user_id);
        $this->assertEquals($exercise_type->sort_index, $created_exercise_type->sort_index);
        $this->assertEquals($exercise_type->id, $created_exercise_type->id);
    }


    /**
     * 種目の追加、名前被り
     */
    public function testAddExerciseTypeException()
    {

        $this->exerciseService = $this->app->make('App\Services\Exercise\ExerciseTypeService');

        $name = 'testAddExerciseTypeException';
        $birth_day = '1999-01-19';

        $auth = Auth::factory()->create(['email' => 'testAddExerciseTypeExceptionType@gmail.com', 'password' => 'pass']);
        $user = User::factory()->create(['name' => $name, 'birth_day' => $birth_day, 'auth_id' => $auth->id]);

        $body_part = BodyPart::factory()->create(['name' => 'shoulder', 'index' => 1]);

        $original_exercise_types = [];
        for ($i = 0; $i < 10; $i++) {
            $exercise_mame = "press" . $i;
            $sort_index = $i + 1;
            $original_exercise_types[] = ExerciseType::factory()->create(['body_part_id' => $body_part->id, 'user_id' => $user->id, 'name' => $exercise_mame, 'sort_index' => $sort_index]);
        }


        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage(__('messages.exercise_type.duplicate'));
        $new_exercise_name = $exercise_mame;
        $this->exerciseService->addExerciseType($user, $body_part->id, $new_exercise_name);

        $after_exercise_types = ExerciseType::where(['body_part_id' => $body_part->id, 'user_id' => $user->id])->get();
        $this->assertEquals(count($original_exercise_types), $after_exercise_types->count());
    }
}
