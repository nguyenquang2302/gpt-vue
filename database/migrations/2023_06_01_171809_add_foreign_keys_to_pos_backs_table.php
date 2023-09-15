<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_backs', function (Blueprint $table) {
            $table->foreign(['bank_log_id'])->references(['id'])->on('bank_logs')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['pos_id'])->references(['id'])->on('pos')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_backs', function (Blueprint $table) {
            $table->dropForeign('pos_backs_bank_log_id_foreign');
            $table->dropForeign('pos_backs_user_id_foreign');
            $table->dropForeign('pos_backs_pos_id_foreign');
        });
    }
};
