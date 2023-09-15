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
        Schema::create('drawal_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('money')->nullable()->default(0);
            $table->double('fee_bank')->nullable()->default(0);
            $table->double('fee_bank_money')->nullable()->default(0);
            $table->double('money_back')->nullable()->default(0);
            $table->timestamp('time')->nullable();
            $table->integer('sort')->default(10);
            $table->string('lo')->nullable();
            $table->string('bill')->nullable();
            $table->unsignedBigInteger('drawal_id')->nullable()->index('drawal_details_drawal_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('drawal_details_user_id_foreign');
            $table->unsignedBigInteger('pos_id')->nullable()->index('drawal_details_pos_id_foreign');
            $table->unsignedBigInteger('customer_card_id')->nullable()->index('drawal_details_customer_card_id_foreign');
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->double('profit')->nullable();
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
        Schema::dropIfExists('drawal_details');
    }
};
