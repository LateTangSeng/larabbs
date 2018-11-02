<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndividualProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('weixin_openid')->unique();
            $table->string('weixin_unionid')->nullable();
            $table->string('fund_code')->nullable();
            $table->string('central_value')->nullable();
            $table->string('base_value')->nullable();
            $table->string('planned_year')->nullable();
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
        Schema::dropIfExists('individual_project');
    }
}
