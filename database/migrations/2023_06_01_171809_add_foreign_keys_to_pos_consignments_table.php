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
        Schema::table('pos_consignments', function (Blueprint $table) {
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
        Schema::table('pos_consignments', function (Blueprint $table) {
            $table->dropForeign('pos_consignments_pos_id_foreign');
        });
    }
};
