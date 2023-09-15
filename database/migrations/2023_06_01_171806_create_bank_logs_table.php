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
        Schema::create('bank_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('postingDate')->nullable();
            $table->timestamp('transactionDate')->nullable();
            $table->string('accountNo')->nullable();
            $table->double('creditAmount')->nullable();
            $table->double('debitAmount')->nullable();
            $table->string('currency')->nullable();
            $table->string('description')->nullable();
            $table->string('availableBalance')->nullable();
            $table->string('beneficiaryAccount')->nullable();
            $table->string('refNo')->nullable();
            $table->string('benAccountName')->nullable();
            $table->string('bankName')->nullable();
            $table->string('benAccountNo')->nullable();
            $table->string('dueDate')->nullable();
            $table->string('transactionType')->nullable();
            $table->string('content')->nullable();
            $table->string('content_fix')->nullable();
            $table->text('note')->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('fund_category_id')->nullable()->index('bank_logs_fund_category_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('bank_logs_user_id_foreign');
            $table->unsignedBigInteger('user_id_belongto')->nullable()->index('bank_logs_user_id_belongto_foreign');
            $table->unsignedTinyInteger('isChecked')->nullable()->default(0);
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_logs');
    }
};
