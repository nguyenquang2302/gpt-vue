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
        Schema::table('drawals', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['user_fee_id'])->references(['id'])->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drawals', function (Blueprint $table) {
            $table->dropForeign('drawals_customer_id_foreign');
            $table->dropForeign('drawals_user_id_foreign');
            $table->dropForeign('drawals_user_fee_id_foreign');
        });
    }
};
