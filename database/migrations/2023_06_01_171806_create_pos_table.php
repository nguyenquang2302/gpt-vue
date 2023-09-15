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
        Schema::create('pos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('currency_limit')->nullable();
            $table->double('currency_limit_on_card')->nullable();
            $table->double('bill_limit_on_card')->nullable();
            $table->double('currency_limit_on_bill')->nullable();
            $table->double('fee_bank')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->integer('sort')->default(10);
            $table->unsignedBigInteger('user_id')->nullable()->index('pos_user_id_foreign');
            $table->unsignedBigInteger('user_id_belongto')->nullable()->index('pos_user_id_belongto_foreign');
            $table->unsignedBigInteger('bank_id')->nullable()->index('pos_bank_id_foreign');
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('telegramChanelId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos');
    }
};
