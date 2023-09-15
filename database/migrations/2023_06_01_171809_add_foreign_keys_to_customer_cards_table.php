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
        Schema::table('customer_cards', function (Blueprint $table) {
            $table->foreign(['bank_id'])->references(['id'])->on('banks')->onDelete('CASCADE');
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
        Schema::table('customer_cards', function (Blueprint $table) {
            $table->dropForeign('customer_cards_bank_id_foreign');
            $table->dropForeign('customer_cards_user_id_foreign');
            $table->dropForeign('customer_cards_customer_id_foreign');
        });
    }
};
