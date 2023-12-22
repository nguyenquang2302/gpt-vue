<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tp_bank_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("refNo")->nullable();
            $table->string("arrangementId")->nullable();
            $table->string("reference")->nullable();
            $table->string("description")->nullable();
            $table->string("category")->nullable();
            $table->timestamp('bookingDate')->nullable();
            $table->timestamp('valueDate')->nullable();
            $table->double('amount')->nullable();
            $table->string("currency")->nullable();
            $table->string("creditDebitIndicator")->nullable();
            $table->double("runningBalance")->nullable();
            $table->string("ofsAcctNo")->nullable(); // tk nhan tien
            $table->string("ofsAcctName")->nullable(); // tk nhan tien
            $table->string("creditorBankNameVn")->nullable();
            $table->string("creditorBankNameEn")->nullable();
            $table->double("creditAmount")->nullable();
            $table->double("debitAmount")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_bank_logs');
    }
};
