<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwitterTokenToAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auths', function (Blueprint $table) {
            $table->string('twitter_token')->nullable()->after('password')->comment('Twitterから取得したトークン');
            $table->string('twitter_token_secret')->nullable()->after('twitter_token')->comment('Twitterから取得したシークレットトークン');
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
        Schema::table('auths', function (Blueprint $table) {
            $table->dropColumn('twitter_token');
            $table->dropColumn('twitter_token_secret');
        });
        Schema::enableForeignKeyConstraints();
    }
}
