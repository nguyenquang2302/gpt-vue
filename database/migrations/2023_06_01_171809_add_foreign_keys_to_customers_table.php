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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign(['district_id'])->references(['id'])->on('districts')->onDelete('CASCADE');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->foreign(['province_id'])->references(['id'])->on('provinces')->onDelete('CASCADE');
            $table->foreign(['ward_id'])->references(['id'])->on('wards')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_district_id_foreign');
            $table->dropForeign('customers_user_id_foreign');
            $table->dropForeign('customers_province_id_foreign');
            $table->dropForeign('customers_ward_id_foreign');
        });
    }
};
