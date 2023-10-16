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
        Schema::create('global_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('totalTransactions')->nullable();
            $table->double('totalDrawals')->nullable();
            $table->integer('totalCustomerNew')->nullable();
            $table->double('fee_ship')->nullable();
            $table->double('expense')->nullable();
            $table->double('totalProfit')->nullable();
            $table->timestamp('perDay')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_global_details');
    }
};
