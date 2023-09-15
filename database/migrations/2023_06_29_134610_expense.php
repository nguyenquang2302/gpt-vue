<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Expense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('debitAmount')->nullable();
            $table->double('creditAmount')->nullable();
            $table->enum('type', ['1', '2'])->default('1');
            $table->unsignedBigInteger('fund_category_id')->nullable()->index('fund_transactions_fund_category_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('fund_transactions_user_id_foreign');
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->string('refNo')->nullable();
            $table->unsignedBigInteger('bank_log_id')->nullable()->index('fund_transactions_bank_log_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense');
    }
}
