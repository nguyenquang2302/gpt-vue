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
        Schema::table('customer_transaction', function (Blueprint $table) {
            $table->foreign(['bank_log_id'])->references(['id'])->on('bank_logs')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_transaction', function (Blueprint $table) {
            $table->dropForeign('customer_transaction_bank_log_id_foreign');
            $table->dropForeign('customer_transaction_user_id_foreign');
            $table->dropForeign('customer_transaction_customer_id_foreign');
        });
    }
};
