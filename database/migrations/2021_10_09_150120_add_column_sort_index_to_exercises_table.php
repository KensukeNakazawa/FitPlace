<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Exercise;


class AddColumnSortIndexToExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'exercises',
            function (Blueprint $table) {
                $table->integer('sort_index')->nullable()->after('set')->comment('表示順序のためのindex');
            }
        );

        $users = User::get();
        foreach ($users as $user) {
            $now_day = new Carbon('2021-06-08');
            $today = new Carbon();

            while ($today->gt($now_day)) {
                $exercises = Exercise::where('user_id', $user->id)->whereDate('exercise_at', $now_day->toDateString())->get();
                $index = 0;
                foreach ($exercises as $exercise) {
                    $index++;
                    $exercise->sort_index = $index;
                    $exercise->save();
                }
                $now_day = $now_day->addDays(1);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'exercises',
            function (Blueprint $table) {
                $table->dropColumn('sort_index');
            }
        );
    }
}
