<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExerciseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercise_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('この種目を持っているユーザー');
            $table->foreignId('body_part_id')->comment('種目の部位');
            $table->string('name');
            $table->float('max_weight', 8, 2)->default(0)->comment('種目の最大拳上重量');
            $table->integer('sort_index')->nullable(true);
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
        Schema::dropIfExists('exercise_types');
        Schema::enableForeignKeyConstraints();
    }
}
