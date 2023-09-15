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
        Schema::create('withdrawal_transaction_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('money')->nullable()->default(0);
            $table->double('money_drawal')->nullable()->default(0);
            $table->double('fee_bank')->nullable();
            $table->double('fee_money_bank')->nullable();
            $table->double('money_back')->nullable()->default(0);
            $table->string('lo')->nullable();
            $table->string('bill')->nullable();
            $table->timestamp('time')->nullable();
            $table->integer('sort')->default(10);
            $table->unsignedBigInteger('withdrawal_id')->nullable()->index('withdrawal_transaction_detail_withdrawal_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('withdrawal_transaction_detail_user_id_foreign');
            $table->unsignedBigInteger('pos_id')->nullable()->index('withdrawal_transaction_detail_pos_id_foreign');
            $table->timestamp('postingDate')->nullable();
            $table->timestamp('transactionDate')->nullable();
            $table->string('refNo')->nullable();
            $table->unsignedTinyInteger('isBankChecked')->nullable()->default(0);
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('bank_log_id')->nullable()->index('withdrawal_transaction_detail_bank_log_id_foreign');
            $table->double('profit')->nullable();
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
        Schema::dropIfExists('withdrawal_transaction_detail');
    }
};
