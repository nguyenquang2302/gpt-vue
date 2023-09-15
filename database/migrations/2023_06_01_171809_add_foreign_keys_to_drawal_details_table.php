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
        Schema::table('drawal_details', function (Blueprint $table) {
            $table->foreign(['customer_card_id'])->references(['id'])->on('customer_cards')->onDelete('CASCADE');
            $table->foreign(['pos_id'])->references(['id'])->on('pos')->onDelete('CASCADE');
            $table->foreign(['drawal_id'])->references(['id'])->on('drawals')->onDelete('CASCADE');
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
        Schema::table('drawal_details', function (Blueprint $table) {
            $table->dropForeign('drawal_details_customer_card_id_foreign');
            $table->dropForeign('drawal_details_pos_id_foreign');
            $table->dropForeign('drawal_details_drawal_id_foreign');
            $table->dropForeign('drawal_details_user_id_foreign');
        });
    }
};
