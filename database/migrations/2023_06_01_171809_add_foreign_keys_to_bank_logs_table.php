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
        Schema::table('bank_logs', function (Blueprint $table) {
            $table->foreign(['fund_category_id'])->references(['id'])->on('fund_categories')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['user_id_belongto'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_logs', function (Blueprint $table) {
            $table->dropForeign('bank_logs_fund_category_id_foreign');
            $table->dropForeign('bank_logs_user_id_foreign');
            $table->dropForeign('bank_logs_user_id_belongto_foreign');
        });
    }
};
