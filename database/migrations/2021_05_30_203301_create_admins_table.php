<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('login_id')->unique()->comment('ログインのID');
            $table->string('password')->comment('パスワ−ド');
            $table->string('name')->unique()->comment('管理者名');
            $table->string('authority')->default(2)->comment('権限');
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
        Schema::dropIfExists('admins');
        Schema::enableForeignKeyConstraints();
    }
}
