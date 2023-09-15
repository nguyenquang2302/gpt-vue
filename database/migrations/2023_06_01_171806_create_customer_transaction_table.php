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
        Schema::create('customer_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('money_before')->nullable();
            $table->double('money_after')->nullable();
            $table->double('money')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->index('customer_transaction_customer_id_foreign');
            $table->string('content')->nullable();
            $table->string('source')->nullable();
            $table->string('refNo')->nullable();
            $table->unsignedBigInteger('key')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_customer_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->timestamp('transactionDate')->nullable();
            $table->timestamp('postingDate')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('customer_transaction_user_id_foreign');
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedTinyInteger('isBankChecked')->nullable()->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('bank_log_id')->nullable()->index('customer_transaction_bank_log_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_transaction');
    }
};
