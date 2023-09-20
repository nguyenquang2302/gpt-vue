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
        Schema::table('drawal_details', function (Blueprint $table) {
            $table->double('fee_partner')->nullable()->default(0);
            $table->unsignedBigInteger('user_partner_id')->nullable();
            $table->double('fee_partner_money')->nullable()->default(0);
            $table->foreign(['user_partner_id'])->references(['id'])->on('users')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    Schema::create('drawal_details', function (Blueprint $table) {
        $table->dropColumn('fee_partner');
        $table->dropColumn('user_partner_id');
        $table->dropColumn('fee_partner_money');
        
    });
        //
    }
};
