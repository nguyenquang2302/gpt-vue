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
        Schema::create('partner_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('debitAmount')->nullable();
            $table->double('creditAmount')->nullable();
            $table->double('money_after')->nullable();
            $table->double('money_before')->nullable();
            $table->enum('type', ['1', '2'])->default('1');
            $table->unsignedBigInteger('user_id')->nullable()->index('fund_transactions_user_id_foreign');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onDelete('CASCADE');
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->string('refNo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_transactions');
    }
};
