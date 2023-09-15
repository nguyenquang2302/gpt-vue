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
        Schema::table('fund_transactions', function (Blueprint $table) {
            $table->foreign(['bank_log_id'])->references(['id'])->on('bank_logs')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['fund_category_id'])->references(['id'])->on('fund_categories')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            $table->dropForeign('fund_transactions_bank_log_id_foreign');
            $table->dropForeign('fund_transactions_user_id_foreign');
            $table->dropForeign('fund_transactions_fund_category_id_foreign');
        });
    }
};
