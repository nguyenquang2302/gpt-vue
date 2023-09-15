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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('cmnd')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();
            $table->text('image')->nullable();
            $table->timestamp('birth_day')->nullable();
            $table->double('money')->nullable()->default(0);
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedBigInteger('province_id')->nullable()->index('customers_province_id_foreign');
            $table->unsignedBigInteger('district_id')->nullable()->index('customers_district_id_foreign');
            $table->unsignedBigInteger('ward_id')->nullable()->index('customers_ward_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('customers_user_id_foreign');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('slug')->nullable();
            $table->unsignedTinyInteger('request_vip')->nullable()->default(0);
            $table->unsignedTinyInteger('vip')->nullable()->default(0);
            $table->timestamp('vip_time')->nullable();
            $table->timestamp('vip_time_expires')->nullable();
            $table->unsignedBigInteger('introduced_by_user_id')->nullable();
            $table->timestamp('last_transaction_time')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
