<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnChangeColumnTypeToLineNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_notifies', function (Blueprint $table) {
            $table->timestamp('check_at')->after('access_token')
                ->nullable()
                ->comment('LineNotify導線を確認した日');
            $table->text('access_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_notifies', function (Blueprint $table) {
            $table->dropColumn('check_at');
            $table->text('access_token')->nullable(false)->change();
        });
    }
}
