<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\PlanExercise;
use App\Models\WeekDay;
use App\Models\BodyPart;

class AddColumnSortIndexToPlanExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_exercises', function (Blueprint $table) {
            $table->integer('sort_index')->nullable()->after('set')->comment('表示順序のためのindex');
        });

        $users = User::get();
        $week_days = WeekDay::get();

        foreach ($users as $user) {
           foreach ($week_days as $week_day) {
               $plan_exercises = PlanExercise::where('user_id', $user->id)->where('week_day_id', $week_day->id)->get();
               $index = 0;
               foreach ($plan_exercises as $plan_exercise) {
                   $index ++;
                   $plan_exercise->sort_index = $index;
                   $plan_exercise->save();
               }
           }
        }

        $body_part = BodyPart::where('name', '脚 & 臀筋')->get()->first();
        if (!empty($body_part)) {
            $body_part->name = '脚';
            $body_part->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_exercises', function (Blueprint $table) {
            $table->dropColumn('sort_index');
        });
    }
}
