<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_notifies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->comment('対象のユーザーID(FK)');
            $table->text('access_token')->comment('LineNotifyのアクセストークン');
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
        Schema::dropIfExists('line_notifies');
    }
}
