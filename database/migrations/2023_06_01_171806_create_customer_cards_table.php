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
        Schema::create('customer_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('start_number')->nullable();
            $table->string('end_number')->nullable();
            $table->integer('day_statement')->nullable();
            $table->double('currency_limit')->nullable();
            $table->enum('type', ['visa', 'master', 'jcb', 'napas', 'america_exprees'])->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->integer('sort')->default(10);
            $table->unsignedBigInteger('customer_id')->nullable()->index('customer_cards_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('customer_cards_user_id_foreign');
            $table->unsignedBigInteger('bank_id')->nullable()->index('customer_cards_bank_id_foreign');
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('due_date')->nullable();
            $table->string('card_number')->nullable();
            $table->timestamp('combo_time')->nullable();
            $table->unsignedTinyInteger('save')->nullable()->default(0);
            $table->timestamp('due_date2')->nullable();
            $table->double('currency_payment')->nullable()->default(0);
            $table->timestamp('date_comlate')->nullable();
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
        Schema::dropIfExists('customer_cards');
    }
};
