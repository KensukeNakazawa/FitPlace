<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->comment('対象のユーザーID');
            $table->foreignId('exercise_type_id')->references('id')->on('exercise_types')->comment('種目のID');
            $table->foreignId('week_day_id')->references('id')->on('week_days')->comment('予定の曜日');
            $table->integer('set')->default(0)->comment('トレーニングのセット数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('plan_exercises');
        Schema::enableForeignKeyConstraints();
    }
}
