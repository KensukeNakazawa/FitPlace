<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedExerciseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deleted_exercise_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('exercise_detail_id')->comment('元のID');
            $table->foreignId('exercise_id')->nullable(true)->comment('その日の対象トレーニング');
            $table->integer('rep')->comment('レップ数');
            $table->float('weight', 8, 2)->comment('セット内の重量');
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
        Schema::dropIfExists('deleted_exercise_details');
    }
}
