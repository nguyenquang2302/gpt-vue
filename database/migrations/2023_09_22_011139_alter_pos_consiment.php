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
        Schema::table('pos_consignments', function (Blueprint $table) {
            $table->double('total_pos')->nullable()->default(0);
            $table->double('total_pos')->nullable()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    Schema::create('customers', function (Blueprint $table) {
        $table->dropColumn('pos_consignments');
        
    });
        //
    }
};
