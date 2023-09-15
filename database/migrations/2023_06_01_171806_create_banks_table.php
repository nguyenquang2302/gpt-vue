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
        Schema::create('banks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedBigInteger('user_id')->nullable()->index('banks_user_id_foreign');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->string('bin')->nullable();
            $table->string('shortName')->nullable();
            $table->string('logo')->nullable();
            $table->string('transferSupported')->nullable();
            $table->string('lookupSupported')->nullable();
            $table->string('short_name')->nullable();
            $table->string('support')->nullable();
            $table->string('isTransfer')->nullable();
            $table->string('swift_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks');
    }
};
