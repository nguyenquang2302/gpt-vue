<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSttDrawalAndWithDrawal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drawals', function (Blueprint $table) {
            $table->integer('stt')->nullable()->default(0);
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->integer('stt')->nullable()->default(0);
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
            $table->dropColumn('stt');
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('stt');
        });
    }
}
