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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('type');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->unsignedTinyInteger('active')->default(1);
            $table->string('timezone')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('to_be_logged_out')->default(false);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('accountName')->nullable();
            $table->string('accountNo')->nullable();
            $table->string('passBank')->nullable();
            $table->unsignedTinyInteger('activeBank')->nullable()->default(0);
            $table->unsignedTinyInteger('autoPosBack')->nullable()->default(0);
            $table->unsignedTinyInteger('branch_id')->nullable()->default(0);
            $table->timestamp('birth_day')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('skype_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('img')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
