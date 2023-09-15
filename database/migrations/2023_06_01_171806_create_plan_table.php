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
        Schema::create('plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('t1')->nullable();
            $table->text('plan1')->nullable();
            $table->text('ex_plan1')->nullable();
            $table->string('t2')->nullable();
            $table->text('plan2')->nullable();
            $table->text('ex_plan2')->nullable();
            $table->string('t3')->nullable();
            $table->text('plan3')->nullable();
            $table->text('ex_plan3')->nullable();
            $table->string('t4')->nullable();
            $table->text('plan4')->nullable();
            $table->text('ex_plan4')->nullable();
            $table->string('t5')->nullable();
            $table->text('plan5')->nullable();
            $table->text('ex_plan5')->nullable();
            $table->string('t6')->nullable();
            $table->text('plan6')->nullable();
            $table->text('ex_plan6')->nullable();
            $table->string('t7')->nullable();
            $table->text('plan7')->nullable();
            $table->text('ex_plan7')->nullable();
            $table->string('t8')->nullable();
            $table->text('plan8')->nullable();
            $table->text('ex_plan8')->nullable();
            $table->string('t9')->nullable();
            $table->text('plan9')->nullable();
            $table->text('ex_plan9')->nullable();
            $table->string('t10')->nullable();
            $table->text('plan10')->nullable();
            $table->text('ex_plan10')->nullable();
            $table->string('t11')->nullable();
            $table->text('plan11')->nullable();
            $table->text('ex_plan11')->nullable();
            $table->string('t12')->nullable();
            $table->text('plan12')->nullable();
            $table->text('ex_plan12')->nullable();
            $table->string('t13')->nullable();
            $table->text('plan13')->nullable();
            $table->text('ex_plan13')->nullable();
            $table->string('t14')->nullable();
            $table->text('plan14')->nullable();
            $table->text('ex_plan14')->nullable();
            $table->string('t15')->nullable();
            $table->text('plan15')->nullable();
            $table->text('ex_plan15')->nullable();
            $table->string('t16')->nullable();
            $table->text('plan16')->nullable();
            $table->text('ex_plan16')->nullable();
            $table->text('your_success')->nullable();
            $table->text('grateful_for')->nullable();
            $table->text('lesson')->nullable();
            $table->text('distraction')->nullable();
            $table->text('not_reached')->nullable();
            $table->unsignedTinyInteger('active')->nullable()->default(1);
            $table->unsignedBigInteger('user_id')->nullable()->index('plan_user_id_foreign');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan');
    }
};
