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
        Schema::create('pos_consignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pos_id')->nullable()->index('pos_consignments_pos_id_foreign');
            $table->string('lo')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedTinyInteger('isDone')->nullable()->default(0);
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
        Schema::dropIfExists('pos_consignments');
    }
};
