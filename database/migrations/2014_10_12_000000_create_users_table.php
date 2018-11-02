<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('weixin_openid')->unique();
            $table->string('weixin_unionid')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('ispaid')->default(false);
            $table->timestamp('first_log_at')->nullable();
            $table->timestamp('last_log_at')->nullable();
            $table->string('weixin_session_key')->nullable();
            $table->string('last_selection')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
