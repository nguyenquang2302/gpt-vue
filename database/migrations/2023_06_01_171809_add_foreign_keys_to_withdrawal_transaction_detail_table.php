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
        Schema::table('withdrawal_transaction_detail', function (Blueprint $table) {
            $table->foreign(['bank_log_id'])->references(['id'])->on('bank_logs')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['pos_id'])->references(['id'])->on('pos')->onDelete('CASCADE');
            $table->foreign(['withdrawal_id'])->references(['id'])->on('withdrawals')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawal_transaction_detail', function (Blueprint $table) {
            $table->dropForeign('withdrawal_transaction_detail_bank_log_id_foreign');
            $table->dropForeign('withdrawal_transaction_detail_user_id_foreign');
            $table->dropForeign('withdrawal_transaction_detail_pos_id_foreign');
            $table->dropForeign('withdrawal_transaction_detail_withdrawal_id_foreign');
        });
    }
};
