<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->references('id')->on('exercises')->comment('その日の対象トレーニング');
            $table->integer('rep')->comment('レップ数');
            $table->float('weight', 8, 2)->default(0)->comment('セット内の重量');
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
        Schema::dropIfExists('exercise_details');
        Schema::enableForeignKeyConstraints();
    }
}
