<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_record', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('weixin_openid')->unique();
            $table->string('weixin_unionid')->nullable();
            $table->string('phone')->nullable();
            $table->string('paidtime')->nullable();
            $table->string('paidmoney')->nullable();
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
        Schema::dropIfExists('payment_record');
    }
}
