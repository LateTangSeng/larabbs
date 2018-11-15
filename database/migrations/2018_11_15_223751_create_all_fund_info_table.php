<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllFundInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FundInfo', function (Blueprint $table) {
            $table->string('FundCode')->nullable()->unique();
            $table->string('FundInit')->nullable();
            $table->string('FundName')->nullable();
            $table->string('FundType')->nullable();
            $table->string('FundPinYin')->nullable();
            $table->string('FundRiskLevel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('FundInfo');
    }
}
