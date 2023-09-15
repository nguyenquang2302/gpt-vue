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
        Schema::create('drawals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('money_drawal')->nullable();
            $table->double('money')->nullable();
            $table->double('fee_customer')->nullable();
            $table->double('fee_ship')->nullable();
            $table->double('fee_user')->nullable();
            $table->double('fee_money_customer')->nullable();
            $table->double('profit_money')->nullable();
            $table->unsignedTinyInteger('isDone')->nullable()->default(0);
            $table->unsignedTinyInteger('isBankChecked')->nullable()->default(0);
            $table->integer('sort')->default(10);
            $table->unsignedBigInteger('customer_id')->nullable()->index('drawals_customer_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('drawals_user_id_foreign');
            $table->unsignedBigInteger('user_fee_id')->nullable()->index('drawals_user_fee_id_foreign');
            $table->unsignedTinyInteger('transfer')->nullable()->default(1);
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank_customer_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedTinyInteger('status')->nullable()->default(0);
            $table->double('profit')->nullable()->default(0);
            $table->timestamp('datetime')->nullable();
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
        Schema::dropIfExists('drawals');
    }
};
