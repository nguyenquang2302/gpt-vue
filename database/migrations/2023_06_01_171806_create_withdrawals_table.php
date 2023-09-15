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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('money_withdrawal')->nullable();
            $table->double('fee_customer')->nullable();
            $table->double('fee_ship')->nullable();
            $table->double('fee_user')->nullable();
            $table->double('fee_money_customer')->nullable();
            $table->double('profit_money')->nullable();
            $table->unsignedTinyInteger('isDone')->nullable()->default(0);
            $table->integer('sort')->default(10);
            $table->unsignedBigInteger('customer_id')->nullable()->index('withdrawals_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('withdrawals_user_id_foreign');
            $table->unsignedBigInteger('user_fee_id')->nullable()->index('withdrawals_user_fee_id_foreign');
            $table->unsignedBigInteger('customer_card_id')->nullable()->index('withdrawals_customer_card_id_foreign');
            $table->unsignedTinyInteger('isBankChecked')->nullable()->default(0);
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedTinyInteger('status')->nullable()->default(0);
            $table->double('profit')->nullable()->default(0);
            $table->timestamp('datetime')->nullable();
            $table->unsignedTinyInteger('isOwnerPos')->nullable()->default(0);
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
        Schema::dropIfExists('withdrawals');
    }
};
