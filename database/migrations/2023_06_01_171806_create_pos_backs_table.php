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
        Schema::create('pos_backs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('lo')->nullable();
            $table->double('money')->nullable()->default(0);
            $table->timestamp('time')->nullable();
            $table->integer('sort')->default(10);
            $table->timestamp('postingDate')->nullable();
            $table->timestamp('transactionDate')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('pos_backs_user_id_foreign');
            $table->unsignedBigInteger('pos_id')->nullable()->index('pos_backs_pos_id_foreign');
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('refNo')->nullable();
            $table->unsignedBigInteger('bank_log_id')->nullable()->index('pos_backs_bank_log_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_backs');
    }
};
