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
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreign(['customer_card_id'])->references(['id'])->on('customer_cards')->onDelete('CASCADE');
            $table->foreign(['user_fee_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign('withdrawals_customer_card_id_foreign');
            $table->dropForeign('withdrawals_user_fee_id_foreign');
            $table->dropForeign('withdrawals_customer_id_foreign');
            $table->dropForeign('withdrawals_user_id_foreign');
        });
    }
};
