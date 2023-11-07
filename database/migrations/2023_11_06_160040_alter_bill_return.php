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
        //
        Schema::table('withdrawal_transaction_detail', function (Blueprint $table) {
            $table->unsignedTinyInteger('bill_return')->default(0);

        });
        Schema::table('drawal_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('bill_return')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('withdrawal_transaction_detail', function (Blueprint $table) {
            $table->dropColumn('bill_return');
        });

        Schema::table('drawal_details', function (Blueprint $table) {
            $table->dropColumn('bill_return');
        });
    }
};
