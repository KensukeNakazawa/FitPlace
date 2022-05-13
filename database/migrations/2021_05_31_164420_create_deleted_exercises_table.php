<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_exercises', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('exercise_id')->comment('元のID');
            $table->foreignId('user_id')->references('id')->on('users')->comment('対象のユーザーID');
            $table->foreignId('exercise_type_id')->references('id')->on('exercise_types')->comment('種目のID');
            $table->integer('set')->default(0)->comment('トレーニングのセット数');
            $table->float('volume')->default(0)->comment('トレーニングのボリューム(重量*回数の総和)');
            $table->float('max')->default(0)->comment('トレーニングの中の最大1RM');
            $table->timestamp('exercise_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('トレーニングした日');
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
        Schema::dropIfExists('deleted_exercises');
    }
}
